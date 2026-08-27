<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Username');
    }

    public function test_admin_can_login_with_username(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'admin_test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'username' => 'admin_test',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_cashier_can_login_with_username(): void
    {
        $cashier = User::factory()->cashier()->create([
            'username' => 'kasir_test',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'username' => 'kasir_test',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('cashier.dashboard'));
        $this->assertAuthenticatedAs($cashier);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->cashier()->inactive()->create([
            'username' => 'inactive_user',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'username' => 'inactive_user',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->cashier()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
