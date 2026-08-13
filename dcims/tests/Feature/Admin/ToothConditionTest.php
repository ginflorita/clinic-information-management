<?php

namespace Tests\Feature\Admin;

use App\Models\ToothCondition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToothConditionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.tooth-conditions.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_condition(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.tooth-conditions.store'), [
                'code' => 'CARIES',
                'name' => 'Caries',
                'category' => 'restorative',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.tooth-conditions.index'));
        $this->assertDatabaseHas('tooth_conditions', ['code' => 'CARIES', 'is_active' => true]);
    }

    public function test_code_must_be_unique(): void
    {
        ToothCondition::factory()->create(['code' => 'CARIES']);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.tooth-conditions.store'), [
                'code' => 'CARIES',
                'name' => 'Duplicate',
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_authenticated_user_can_update_a_condition(): void
    {
        $condition = ToothCondition::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.tooth-conditions.update', $condition), [
                'code' => $condition->code,
                'name' => 'Updated Name',
            ])
            ->assertRedirect(route('admin.tooth-conditions.index'));

        $this->assertDatabaseHas('tooth_conditions', ['id' => $condition->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_condition(): void
    {
        $condition = ToothCondition::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.tooth-conditions.destroy', $condition))
            ->assertRedirect(route('admin.tooth-conditions.index'));

        $this->assertSoftDeleted($condition);
    }
}
