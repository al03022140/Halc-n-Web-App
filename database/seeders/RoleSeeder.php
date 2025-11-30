<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Roles requeridos por el sistema
        $roles = ['Admin', 'Sales', 'Purchasing', 'Warehouse', 'Route'];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role], ['name' => $role]);
        }
    }
}
