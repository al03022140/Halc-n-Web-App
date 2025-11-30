<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'invoice_number' => strtoupper($this->faker->unique()->bothify('INV####??')),
            'customer_name' => $this->faker->company(),
            'customer_number' => strtoupper($this->faker->unique()->bothify('CLI###')),
            'fiscal_data' => strtoupper($this->faker->bothify('RFC######??')),
            'order_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'delivery_address' => $this->faker->address(),
            'notes' => $this->faker->optional()->sentence(),
            'user_id' => fn() => User::inRandomOrder()->value('id') ?? User::factory()->create()->id,
            'client_id' => fn() => Client::inRandomOrder()->value('id') ?? Client::factory()->create()->id,
            'product_id' => fn() => Product::inRandomOrder()->value('id') ?? Product::factory()->create()->id,
            'status_id' => fn() => OrderStatus::inRandomOrder()->value('id') ?? OrderStatus::factory()->create()->id,
            'quantity' => $this->faker->numberBetween(1, 10),
            'route_user_id' => fn() => User::inRandomOrder()->value('id'),
            'has_incident' => false,
        ];
    }
}