<?php

namespace Tests\Feature\Admin;

use App\Models\InventoryUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.inventory-units.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_unit(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.inventory-units.store'), [
                'name' => 'Box',
                'abbreviation' => 'bx',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.inventory-units.index'));
        $this->assertDatabaseHas('inventory_units', ['name' => 'Box', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_unit(): void
    {
        $unit = InventoryUnit::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.inventory-units.update', $unit), ['name' => 'Updated Name'])
            ->assertRedirect(route('admin.inventory-units.index'));

        $this->assertDatabaseHas('inventory_units', ['id' => $unit->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_unit(): void
    {
        $unit = InventoryUnit::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.inventory-units.destroy', $unit))
            ->assertRedirect(route('admin.inventory-units.index'));

        $this->assertSoftDeleted($unit);
    }
}
