<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\InvoiceNumberGenerator;
use Database\Seeders\OrderStatusSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SystemSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
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

    public function test_admin_updates_invoice_sequence_and_generator_uses_values(): void
    {
        $admin = $this->makeUserWithRole('Admin');

        $response = $this->actingAs($admin)->put(route('settings.system.update'), [
            'invoice_prefix' => 'ALFA-',
            'invoice_next_number' => 15,
            'require_fiscal_data' => 1,
            'require_delivery_address' => 1,
            'purchasing_alert_email' => 'compras@example.com',
            'slack_stock_webhook' => 'https://hooks.slack.com/services/test',
        ]);

        $response->assertRedirect(route('settings.system'));

        $generator = app(InvoiceNumberGenerator::class);
        $firstInvoice = $generator->generate();

        $this->assertSame('ALFA-000015', $firstInvoice);
        $this->assertSame(16, (int) SystemSetting::value('invoice_next_number'));
    }

    public function test_non_admin_cannot_update_settings(): void
    {
        $sales = $this->makeUserWithRole('Sales');

        $response = $this->actingAs($sales)->put(route('settings.system.update'), [
            'invoice_prefix' => 'FAIL-',
            'invoice_next_number' => 99,
        ]);

        $response->assertForbidden();
    }

    private function makeUserWithRole(string $roleName): User
    {
        $roleId = Role::where('name', $roleName)->value('id');

        return User::factory()->create([
            'role_id' => $roleId,
            'active' => true,
        ]);
    }
}
