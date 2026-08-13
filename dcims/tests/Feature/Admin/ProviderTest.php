<?php

namespace Tests\Feature\Admin;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.providers.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_provider(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.providers.store'), [
                'first_name' => 'Jane',
                'last_name' => 'Dela Cruz',
                'role' => 'dentist',
                'email' => 'jane@example.com',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.providers.index'));
        $this->assertDatabaseHas('providers', ['first_name' => 'Jane', 'last_name' => 'Dela Cruz', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_provider(): void
    {
        $provider = Provider::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.providers.update', $provider), [
                'first_name' => 'Updated',
                'last_name' => $provider->last_name,
                'role' => $provider->role,
            ])
            ->assertRedirect(route('admin.providers.index'));

        $this->assertDatabaseHas('providers', ['id' => $provider->id, 'first_name' => 'Updated', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_provider(): void
    {
        $provider = Provider::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.providers.destroy', $provider))
            ->assertRedirect(route('admin.providers.index'));

        $this->assertSoftDeleted($provider);
    }
}
