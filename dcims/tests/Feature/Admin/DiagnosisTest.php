<?php

namespace Tests\Feature\Admin;

use App\Models\Diagnosis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiagnosisTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.diagnoses.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_diagnosis(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.diagnoses.store'), [
                'code' => 'K02.9',
                'name' => 'Dental caries, unspecified',
                'category' => 'caries',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.diagnoses.index'));
        $this->assertDatabaseHas('diagnoses', ['code' => 'K02.9', 'is_active' => true]);
    }

    public function test_code_must_be_unique(): void
    {
        Diagnosis::factory()->create(['code' => 'K02.9']);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.diagnoses.store'), [
                'code' => 'K02.9',
                'name' => 'Duplicate',
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_authenticated_user_can_update_a_diagnosis(): void
    {
        $diagnosis = Diagnosis::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.diagnoses.update', $diagnosis), [
                'code' => $diagnosis->code,
                'name' => 'Updated Name',
            ])
            ->assertRedirect(route('admin.diagnoses.index'));

        $this->assertDatabaseHas('diagnoses', ['id' => $diagnosis->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_diagnosis(): void
    {
        $diagnosis = Diagnosis::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.diagnoses.destroy', $diagnosis))
            ->assertRedirect(route('admin.diagnoses.index'));

        $this->assertSoftDeleted($diagnosis);
    }
}
