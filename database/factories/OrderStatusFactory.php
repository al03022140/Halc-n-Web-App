<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusFactory extends Factory
{
    protected $model = OrderStatus::class;

    public function definition(): array
    {
        $statuses = [
            ['name' => 'Ordered', 'color' => '#6c757d'],
            ['name' => 'In process', 'color' => '#ffc107'],
            ['name' => 'In route', 'color' => '#17a2b8'],
            ['name' => 'Delivered', 'color' => '#28a745'],
        ];
        $status = $this->faker->randomElement($statuses);
        return $status;
    }
}