<?php

namespace Tests\Feature\Admin;

use App\Models\Chair;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChairTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.chairs.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_chair(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.chairs.store'), [
                'name' => 'Chair 01',
                'location' => 'Room A',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.chairs.index'));
        $this->assertDatabaseHas('chairs', ['name' => 'Chair 01', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_chair(): void
    {
        $chair = Chair::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.chairs.update', $chair), ['name' => 'Updated Name', 'location' => $chair->location])
            ->assertRedirect(route('admin.chairs.index'));

        $this->assertDatabaseHas('chairs', ['id' => $chair->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_chair(): void
    {
        $chair = Chair::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.chairs.destroy', $chair))
            ->assertRedirect(route('admin.chairs.index'));

        $this->assertSoftDeleted($chair);
    }
}
