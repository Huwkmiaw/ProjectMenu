<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_access_dashboard(): void
    {
        $cashier = User::factory()->cashier()->create();

        $response = $this->actingAs($cashier)->get(route('cashier.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Kasir');
    }

    public function test_cashier_can_process_cash_payment_with_change(): void
    {
        $cashier = User::factory()->cashier()->create();
        $order = Order::factory()->pending()->create(['total' => 35000]);

        $response = $this->actingAs($cashier)->patchJson(route('cashier.orders.pay', $order), [
            'payment_method' => 'cash',
            'amount_paid' => 50000,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'payment_method' => 'cash',
            'amount_paid' => 50000,
            'change_amount' => 15000,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
            'payment_method' => 'cash',
            'amount_paid' => 50000,
            'change_amount' => 15000,
            'cashier_id' => $cashier->id,
        ]);
    }

    public function test_cashier_can_process_cashless_payment(): void
    {
        $cashier = User::factory()->cashier()->create();
        $order = Order::factory()->pending()->create(['total' => 50000]);

        $response = $this->actingAs($cashier)->patch(route('cashier.orders.pay', $order), [
            'payment_method' => 'cashless',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
            'payment_method' => 'cashless',
            'cashier_id' => $cashier->id,
        ]);
    }

    public function test_cashier_cannot_pay_less_than_total_in_cash(): void
    {
        $cashier = User::factory()->cashier()->create();
        $order = Order::factory()->pending()->create(['total' => 50000]);

        $response = $this->actingAs($cashier)->patchJson(route('cashier.orders.pay', $order), [
            'payment_method' => 'cash',
            'amount_paid' => 30000,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_cashier_can_cancel_order(): void
    {
        $cashier = User::factory()->cashier()->create();
        $order = Order::factory()->pending()->create();

        $response = $this->actingAs($cashier)->patch(route('cashier.orders.cancel', $order));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cashier_can_poll_pending_orders_as_json(): void
    {
        $cashier = User::factory()->cashier()->create();
        $order = Order::factory()->pending()->create(['customer_name' => 'John Doe']);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'menu_item_name' => 'Es Teh Manis',
            'quantity' => 2,
            'subtotal' => 10000,
        ]);

        $response = $this->actingAs($cashier)->getJson(route('cashier.orders.pending'));

        $response->assertStatus(200);
        $response->assertJsonFragment(['customer_name' => 'John Doe']);
    }
}
