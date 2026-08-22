<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_with_no_role_has_full_access(): void
    {
        $user = User::factory()->create(['role_id' => null]);

        $this->assertTrue($user->canAccessModule('patients'));
        $this->assertTrue($user->canAccessModule('invoices'));
        $this->assertTrue($user->canAccessModule('master_data'));
    }

    public function test_an_admin_has_full_access_regardless_of_role(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->create(['module' => 'patients']);
        $admin = User::factory()->admin()->withRole($role)->create();

        $this->assertTrue($admin->canAccessModule('invoices'));
        $this->assertTrue($admin->canAccessModule('master_data'));
    }

    public function test_a_user_can_only_access_modules_granted_by_their_role(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->create(['module' => 'patients']);
        $user = User::factory()->withRole($role)->create();

        $this->assertTrue($user->canAccessModule('patients'));
        $this->assertFalse($user->canAccessModule('invoices'));
    }

    public function test_a_restricted_user_is_forbidden_from_a_module_not_on_their_role(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->create(['module' => 'patients']);
        $user = User::factory()->withRole($role)->create();

        $this->actingAs($user)->get(route('invoices.index'))->assertForbidden();
        $this->actingAs($user)->get(route('inventory.index'))->assertForbidden();
    }

    public function test_a_restricted_user_can_access_a_module_granted_by_their_role(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->create(['module' => 'patients']);
        $user = User::factory()->withRole($role)->create();

        $this->actingAs($user)->get(route('patients.index'))->assertOk();
    }

    public function test_master_data_module_gates_the_admin_prefixed_master_data_routes(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->create(['module' => 'patients']);
        $user = User::factory()->withRole($role)->create();

        $this->actingAs($user)->get(route('admin.procedures.index'))->assertForbidden();
    }
}
