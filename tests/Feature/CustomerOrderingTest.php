<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_welcome_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dine In');
        $response->assertSee('Take Away');
    }

    public function test_customer_can_set_dine_in(): void
    {
        $response = $this->post('/order-type', [
            'order_type' => 'dine_in',
        ]);

        $response->assertRedirect(route('menu.index'));
        $response->assertSessionHas('order_type', 'dine_in');
    }

    public function test_customer_can_set_take_away_without_table_number(): void
    {
        $response = $this->post('/order-type', [
            'order_type' => 'take_away',
        ]);

        $response->assertRedirect(route('menu.index'));
        $response->assertSessionHas('order_type', 'take_away');
    }

    public function test_customer_can_view_menu_list(): void
    {
        $category = Category::factory()->create(['name' => 'Makanan Utama', 'is_active' => true]);
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'name' => 'Nasi Goreng Spesial',
            'is_available' => true,
        ]);

        $response = $this->withSession(['order_type' => 'take_away'])
            ->get('/menu');

        $response->assertStatus(200);
        $response->assertSee('Nasi Goreng Spesial');
    }

    public function test_customer_can_add_item_to_cart(): void
    {
        $menuItem = MenuItem::factory()->create(['is_available' => true, 'price' => 20000]);

        $response = $this->withSession(['order_type' => 'take_away'])
            ->postJson('/cart/add', [
                'menu_item_id' => $menuItem->id,
                'quantity' => 2,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart_count' => 2,
        ]);
        $this->assertEquals(40000, session('cart')[$menuItem->id]['subtotal']);
    }

    public function test_customer_can_update_and_remove_cart_item(): void
    {
        $menuItem = MenuItem::factory()->create(['is_available' => true, 'price' => 10000]);

        $cart = [
            $menuItem->id => [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => 10000,
                'quantity' => 1,
                'subtotal' => 10000,
            ],
        ];

        // Update quantity
        $response = $this->withSession(['order_type' => 'take_away', 'cart' => $cart])
            ->putJson("/cart/{$menuItem->id}", ['quantity' => 3]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'item_subtotal' => 30000,
            'cart_count' => 3,
        ]);

        // Remove item
        $response = $this->withSession(['order_type' => 'take_away', 'cart' => $cart])
            ->deleteJson("/cart/{$menuItem->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'cart_count' => 0]);
    }

    public function test_customer_can_checkout_order(): void
    {
        $menuItem = MenuItem::factory()->create(['name' => 'Soto Ayam', 'price' => 15000, 'is_available' => true]);

        $cart = [
            $menuItem->id => [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => 15000,
                'quantity' => 2,
                'subtotal' => 30000,
            ],
        ];

        $response = $this->withSession([
            'order_type' => 'dine_in',
            'table_number' => '3',
            'cart' => $cart,
        ])->post('/checkout', [
            'customer_name' => 'Budi Santoso',
            'customer_note' => 'Jangan terlalu asin',
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Budi Santoso',
            'order_type' => 'dine_in',
            'table_number' => '3',
            'status' => 'pending',
            'total' => 30000,
        ]);

        $order = Order::where('customer_name', 'Budi Santoso')->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('welcome'));
        $response->assertSessionHas('order_success');

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'menu_item_name' => 'Soto Ayam',
            'quantity' => 2,
            'subtotal' => 30000,
        ]);
    }

    public function test_order_code_sequence_resets_daily(): void
    {
        $todayStr = now()->format('Ymd');
        $code1 = Order::generateOrderCode();
        $this->assertEquals("ORD-{$todayStr}-0001", $code1);

        Order::factory()->create(['order_code' => $code1]);

        $code2 = Order::generateOrderCode();
        $this->assertEquals("ORD-{$todayStr}-0002", $code2);
    }
}
