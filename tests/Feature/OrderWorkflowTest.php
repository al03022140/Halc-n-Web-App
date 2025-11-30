<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\OrderStatusSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SystemSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            SystemSettingSeeder::class,
            RoleSeeder::class,
            OrderStatusSeeder::class,
        ]);
    }

    public function test_sales_role_cannot_change_order_status(): void
    {
        $salesUser = $this->makeUserWithRole('Sales');
        $order = $this->makeOrderWithStatus('Ordered');
        $targetStatus = OrderStatus::where('name', 'In process')->firstOrFail();

        $response = $this->actingAs($salesUser)->post(route('orders.changeStatus', $order), [
            'status_id' => $targetStatus->id,
        ]);

        $response->assertForbidden();
        $this->assertSame('Ordered', $order->fresh()->status->name);
    }

    public function test_warehouse_can_advance_order_to_in_process(): void
    {
        $warehouseUser = $this->makeUserWithRole('Warehouse');
        $order = $this->makeOrderWithStatus('Ordered');
        $targetStatus = OrderStatus::where('name', 'In process')->firstOrFail();

        $response = $this->actingAs($warehouseUser)->post(route('orders.changeStatus', $order), [
            'status_id' => $targetStatus->id,
            'status_notes' => 'Pedido preparado',
        ]);

        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame('In process', $order->fresh()->status->name);
    }

    private function makeUserWithRole(string $roleName): User
    {
        $roleId = Role::where('name', $roleName)->value('id');
        return User::factory()->create([
            'role_id' => $roleId,
            'active' => true,
        ]);
    }

    private function makeOrderWithStatus(string $statusName): Order
    {
        $statusId = OrderStatus::where('name', $statusName)->firstOrFail()->id;
        $client = Client::factory()->create([
            'fiscal_data' => 'RFC123456789',
        ]);
        $product = Product::factory()->create();
        $user = $this->makeUserWithRole('Sales');

        return Order::factory()->create([
            'status_id' => $statusId,
            'client_id' => $client->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'user_id' => $user->id,
        ]);
    }
}
