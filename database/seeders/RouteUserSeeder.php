<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class RouteUserSeeder extends Seeder
{
    public function run(): void
    {
        $routeRole = Role::where('name', 'Route')->first();
        $routeDept = Department::where('name', 'Route')->first();

        User::updateOrCreate([
            'email' => 'route@halcon.com',
        ], [
            'name' => 'Usuario Route',
            'password' => Hash::make('route123'),
            'role_id' => $routeRole?->id ?? 5,
            'department_id' => $routeDept?->id ?? 4,
            'active' => true,
        ]);
    }
}