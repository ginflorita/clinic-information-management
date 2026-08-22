<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_the_roles_index(): void
    {
        $this->get(route('admin.roles.index'))->assertRedirect(route('login'));
    }

    public function test_a_non_admin_user_cannot_access_the_roles_index(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_an_admin_can_create_a_role_with_module_permissions(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.roles.store'), [
                'name' => 'Receptionist',
                'description' => 'Front desk staff.',
                'modules' => ['patients', 'appointments', 'queue'],
            ]);

        $response->assertRedirect(route('admin.roles.index'));

        $role = Role::where('name', 'Receptionist')->firstOrFail();
        $this->assertSame(['appointments', 'patients', 'queue'], $role->permissions()->pluck('module')->sort()->values()->all());
    }

    public function test_an_admin_can_update_a_roles_module_permissions(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->create(['module' => 'patients']);
        $role->permissions()->create(['module' => 'appointments']);

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.roles.update', $role), [
                'name' => $role->name,
                'modules' => ['patients', 'invoices'],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertSame(['invoices', 'patients'], $role->permissions()->pluck('module')->sort()->values()->all());
    }

    public function test_a_role_can_be_saved_with_no_modules(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.roles.store'), [
                'name' => 'Empty Role',
            ])
            ->assertRedirect(route('admin.roles.index'));

        $role = Role::where('name', 'Empty Role')->firstOrFail();
        $this->assertSame(0, $role->permissions()->count());
    }

    public function test_a_role_still_assigned_to_users_cannot_be_deleted(): void
    {
        $role = Role::factory()->create();
        User::factory()->withRole($role)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.roles.destroy', $role))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    public function test_a_role_with_no_users_can_be_deleted(): void
    {
        $role = Role::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('admin.roles.index'));

        $this->assertSoftDeleted($role);
    }
}
