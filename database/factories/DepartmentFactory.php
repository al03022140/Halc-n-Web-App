<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $depts = ['Ventas', 'Logística', 'Operaciones', 'Soporte'];
        return [
            'name' => $this->faker->randomElement($depts),
        ];
    }
}