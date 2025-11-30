<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $roles = ['Admin', 'Manager', 'Operator', 'Viewer'];
        return [
            'name' => $this->faker->randomElement($roles),
        ];
    }
}