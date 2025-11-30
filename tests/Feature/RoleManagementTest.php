<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_update_role(): void
    {
        $admin = $this->makeUserWithRole('Admin');
        $role = Role::create(['name' => 'QA']);

        $response = $this->actingAs($admin)->put(route('roles.update', $role), [
            'name' => 'Quality Assurance',
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertSame('Quality Assurance', $role->fresh()->name);
    }

    public function test_non_admin_cannot_access_roles_index(): void
    {
        $sales = $this->makeUserWithRole('Sales');

        $response = $this->actingAs($sales)->get(route('roles.index'));

        $response->assertStatus(403);
    }

    private function makeUserWithRole(string $roleName): User
    {
        $roleId = Role::where('name', $roleName)->value('id');

        return User::factory()->create([
            'name' => $roleName . ' User',
            'email' => strtolower($roleName) . '@example.com',
            'password' => Hash::make('password'),
            'role_id' => $roleId,
            'active' => true,
        ]);
    }
}
