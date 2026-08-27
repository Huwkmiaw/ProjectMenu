<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
    }

    public function test_cashier_cannot_access_admin_dashboard(): void
    {
        $cashier = User::factory()->cashier()->create();

        $response = $this->actingAs($cashier)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Minuman Segar',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Minuman Segar',
            'slug' => 'minuman-segar',
        ]);
    }

    public function test_admin_can_create_menu_item(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.menus.store'), [
            'category_id' => $category->id,
            'name' => 'Bakso Urat Super',
            'description' => 'Bakso daging sapi asli',
            'price' => 25000,
            'sort_order' => 1,
            'is_available' => '1',
        ]);

        $response->assertRedirect(route('admin.menus.index'));
        $this->assertDatabaseHas('menu_items', [
            'name' => 'Bakso Urat Super',
            'slug' => 'bakso-urat-super',
            'price' => 25000,
        ]);
    }

    public function test_admin_can_create_cashier_account(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.cashiers.store'), [
            'name' => 'Bambang Kasir',
            'username' => 'bambang123',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.cashiers.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'bambang123',
            'role' => 'cashier',
        ]);
    }

    public function test_admin_can_view_and_export_sales_reports(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->completed()->create(['total' => 50000]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan');

        $csvResponse = $this->actingAs($admin)->get(route('admin.reports.export-csv'));
        $csvResponse->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $csvResponse->headers->get('Content-Type'));
    }
}
