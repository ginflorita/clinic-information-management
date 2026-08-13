<?php

namespace Tests\Feature\Admin;

use App\Models\ProcedureCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcedureCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.procedure-categories.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_category(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.procedure-categories.store'), [
                'name' => 'Restorative',
                'description' => 'Fillings and crowns',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.procedure-categories.index'));
        $this->assertDatabaseHas('procedure_categories', ['name' => 'Restorative', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_category(): void
    {
        $category = ProcedureCategory::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.procedure-categories.update', $category), [
                'name' => 'Updated Name',
                'description' => $category->description,
            ])
            ->assertRedirect(route('admin.procedure-categories.index'));

        $this->assertDatabaseHas('procedure_categories', ['id' => $category->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_category(): void
    {
        $category = ProcedureCategory::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.procedure-categories.destroy', $category))
            ->assertRedirect(route('admin.procedure-categories.index'));

        $this->assertSoftDeleted($category);
    }
}
