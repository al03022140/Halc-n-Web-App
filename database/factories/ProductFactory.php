<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(8),
            'price' => $this->faker->randomFloat(2, 10, 5000),
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####??')),
            'stock' => $this->faker->numberBetween(0, 1000),
            'reorder_level' => $this->faker->numberBetween(25, 100),
            'is_active' => true,
        ];
    }
}