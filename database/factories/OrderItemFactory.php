<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomElement([10000, 15000, 20000, 25000]);
        $qty = fake()->numberBetween(1, 4);

        return [
            'order_id' => Order::factory(),
            'menu_item_id' => MenuItem::factory(),
            'menu_item_name' => fake()->words(2, true),
            'menu_item_price' => $price,
            'quantity' => $qty,
            'subtotal' => $price * $qty,
        ];
    }
}
