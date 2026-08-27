<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isDineIn = fake()->boolean(70);

        return [
            'order_code' => 'ORD-'.strtoupper(Str::random(6)),
            'order_type' => $isDineIn ? 'dine_in' : 'take_away',
            'customer_name' => fake()->name(),
            'table_number' => $isDineIn ? (string) fake()->numberBetween(1, 20) : null,
            'customer_note' => fake()->optional()->sentence(5),
            'status' => 'pending',
            'total' => fake()->randomElement([20000, 35000, 50000, 75000, 100000]),
            'session_id' => Str::random(40),
            'cashier_id' => null,
            'confirmed_at' => null,
            'paid_at' => null,
            'completed_at' => null,
        ];
    }

    /**
     * State: pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * State: confirmed
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'cashier_id' => User::factory()->cashier(),
            'confirmed_at' => now(),
        ]);
    }

    /**
     * State: paid
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'cashier_id' => User::factory()->cashier(),
            'confirmed_at' => now()->subMinutes(10),
            'paid_at' => now(),
        ]);
    }

    /**
     * State: completed
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'cashier_id' => User::factory()->cashier(),
            'confirmed_at' => now()->subMinutes(20),
            'paid_at' => now()->subMinutes(10),
            'completed_at' => now(),
        ]);
    }

    /**
     * State: cancelled
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cashier_id' => User::factory()->cashier(),
        ]);
    }

    /**
     * State: dine_in
     */
    public function dineIn(?string $tableNumber = null): static
    {
        return $this->state(fn (array $attributes) => [
            'order_type' => 'dine_in',
            'table_number' => $tableNumber ?? (string) fake()->numberBetween(1, 20),
        ]);
    }

    /**
     * State: take_away
     */
    public function takeAway(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_type' => 'take_away',
            'table_number' => null,
        ]);
    }
}
