<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        $salesDept = \App\Models\Department::where('name', 'Sales')->first();

        User::updateOrCreate([
            'email' => 'admin@halcon.com',
        ], [
            'name' => 'Admin General',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole?->id ?? 1,
            'department_id' => $salesDept?->id ?? 1,
        ]);
    }
}
