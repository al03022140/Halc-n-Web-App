<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderStatus;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        $status = OrderStatus::firstOrCreate([
            'name' => 'Ordered',
        ], [
            'color' => '#6c757d',
        ]);

        $client = Client::firstOrCreate([
            'name' => 'Construcciones MX',
        ], [
            'phone' => '555-123-4567',
            'email' => 'contacto@construcciones.mx',
            'address' => 'Av. Principal 123, CDMX',
            'fiscal_data' => 'RFC123456XYZ',
            'is_active' => true,
        ]);

        $product = Product::firstOrCreate([
            'sku' => 'SKU-INV1001',
        ], [
            'name' => 'Material de construcción',
            'description' => 'Cemento y varillas',
            'price' => 1500.00,
            'stock' => 100,
            'is_active' => true,
        ]);

        Order::updateOrCreate([
            'invoice_number' => 'INV1001',
        ], [
            'user_id' => $user->id,
            'route_user_id' => $user->id,
            'status_id' => $status->id,
            'client_id' => $client->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'customer_number' => 'CLI001',
            'customer_name' => 'Construcciones MX',
            'fiscal_data' => $client->fiscal_data,
            'order_date' => now(),
            'delivery_address' => 'Av. Principal 123, CDMX',
            'notes' => 'Entrega urgente',
            'has_incident' => false,
        ]);
    }
}
