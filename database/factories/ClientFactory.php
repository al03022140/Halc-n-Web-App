<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'custom_id' => strtoupper($this->faker->bothify('CLI-####')),
            'name' => $this->faker->company(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'fiscal_data' => strtoupper($this->faker->bothify('???########')),
            'is_active' => true,
        ];
    }
}