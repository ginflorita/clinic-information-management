<?php

namespace Tests\Feature\Admin;

use App\Models\InventoryCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.inventory-categories.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_category(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.inventory-categories.store'), [
                'name' => 'PPE',
                'description' => 'Gloves and masks',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.inventory-categories.index'));
        $this->assertDatabaseHas('inventory_categories', ['name' => 'PPE', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_category(): void
    {
        $category = InventoryCategory::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.inventory-categories.update', $category), [
                'name' => 'Updated Name',
                'description' => $category->description,
            ])
            ->assertRedirect(route('admin.inventory-categories.index'));

        $this->assertDatabaseHas('inventory_categories', ['id' => $category->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_category(): void
    {
        $category = InventoryCategory::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.inventory-categories.destroy', $category))
            ->assertRedirect(route('admin.inventory-categories.index'));

        $this->assertSoftDeleted($category);
    }
}
