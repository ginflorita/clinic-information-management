<?php

namespace Tests\Feature\Admin;

use App\Models\RecallType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecallTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.recall-types.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_recall_type(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.recall-types.store'), [
                'name' => '6-Month Cleaning',
                'default_interval_months' => 6,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.recall-types.index'));
        $this->assertDatabaseHas('recall_types', ['name' => '6-Month Cleaning', 'default_interval_months' => 6, 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_recall_type(): void
    {
        $recallType = RecallType::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.recall-types.update', $recallType), [
                'name' => 'Updated Name',
            ])
            ->assertRedirect(route('admin.recall-types.index'));

        $this->assertDatabaseHas('recall_types', ['id' => $recallType->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_recall_type(): void
    {
        $recallType = RecallType::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.recall-types.destroy', $recallType))
            ->assertRedirect(route('admin.recall-types.index'));

        $this->assertSoftDeleted($recallType);
    }
}
