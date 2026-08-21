<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_the_users_index(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    }

    public function test_a_non_admin_user_cannot_access_the_users_index(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_an_admin_can_view_the_users_index(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_an_admin_can_create_a_new_user(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.users.store'), [
                'name' => 'Jane Tester',
                'email' => 'jane.tester@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('admin.users.index'));

        $created = User::where('email', 'jane.tester@example.com')->firstOrFail();
        $this->assertFalse($created->is_admin);
        $this->assertTrue($created->is_active);
        $this->assertNotNull($created->email_verified_at);
    }

    public function test_a_newly_created_user_can_log_in_immediately(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.users.store'), [
                'name' => 'Jane Tester',
                'email' => 'jane.tester@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $this->post('/logout');

        $this->post('/login', [
            'email' => 'jane.tester@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
    }

    public function test_a_non_admin_user_cannot_create_a_user(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->post(route('admin.users.store'), [
                'name' => 'Jane Tester',
                'email' => 'jane.tester@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'jane.tester@example.com']);
    }

    public function test_creating_a_user_requires_a_unique_email(): void
    {
        $existing = User::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.users.store'), [
                'name' => 'Jane Tester',
                'email' => $existing->email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_an_admin_can_update_a_user_without_changing_the_password(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $originalPassword = $user->password;

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.users.update', $user), [
                'name' => 'New Name',
                'email' => $user->email,
                'is_admin' => '1',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertTrue($user->is_admin);
        $this->assertSame($originalPassword, $user->password);
    }

    public function test_deactivating_a_user_prevents_them_from_logging_in(): void
    {
        $user = User::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => '0',
            ]);

        $this->post('/logout');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_an_admin_cannot_demote_or_deactivate_their_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'is_admin' => '0',
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.users.index'));

        $admin->refresh();
        $this->assertTrue($admin->is_admin);
        $this->assertTrue($admin->is_active);
    }
}
