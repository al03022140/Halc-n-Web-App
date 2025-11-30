<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderStatus;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Ordered', 'color' => '#6c757d'],
            ['name' => 'In process', 'color' => '#ffc107'],
            ['name' => 'In route', 'color' => '#17a2b8'],
            ['name' => 'Delivered', 'color' => '#28a745'],
        ];

        foreach ($statuses as $status) {
            OrderStatus::updateOrCreate(['name' => $status['name']], $status);
        }
    }
}
