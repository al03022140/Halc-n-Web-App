<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        return [
            'order_id' => fn() => Order::inRandomOrder()->value('id') ?? Order::factory()->create()->id,
            'path' => 'orders/' . $this->faker->uuid() . '.jpg',
            'type' => $this->faker->randomElement(['en_ruta', 'entrega']),
            'notes' => $this->faker->sentence(),
        ];
    }
}