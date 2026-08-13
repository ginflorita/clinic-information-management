<?php

namespace Tests\Feature\Admin;

use App\Models\Procedure;
use App\Models\ProcedureCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcedureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.procedures.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_procedure_with_a_category(): void
    {
        $category = ProcedureCategory::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.procedures.store'), [
                'procedure_category_id' => $category->id,
                'code' => 'PRC-001',
                'name' => 'Tooth Extraction',
                'default_fee' => 1500,
                'default_duration_minutes' => 45,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.procedures.index'));
        $this->assertDatabaseHas('procedures', [
            'code' => 'PRC-001',
            'procedure_category_id' => $category->id,
            'is_active' => true,
        ]);
    }

    public function test_code_must_be_unique(): void
    {
        Procedure::factory()->create(['code' => 'PRC-001']);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.procedures.store'), [
                'code' => 'PRC-001',
                'name' => 'Duplicate',
                'default_fee' => 100,
                'default_duration_minutes' => 30,
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_authenticated_user_can_update_a_procedure(): void
    {
        $procedure = Procedure::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.procedures.update', $procedure), [
                'code' => $procedure->code,
                'name' => 'Updated Name',
                'default_fee' => $procedure->default_fee,
                'default_duration_minutes' => $procedure->default_duration_minutes,
            ])
            ->assertRedirect(route('admin.procedures.index'));

        $this->assertDatabaseHas('procedures', ['id' => $procedure->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_procedure(): void
    {
        $procedure = Procedure::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.procedures.destroy', $procedure))
            ->assertRedirect(route('admin.procedures.index'));

        $this->assertSoftDeleted($procedure);
    }
}
