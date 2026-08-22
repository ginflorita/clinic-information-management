<?php

namespace Tests\Feature\Admin;

use App\Models\Medication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.medications.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_medication(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.medications.store'), [
                'generic_name' => 'Amoxicillin',
                'brand_name' => 'Amoxil',
                'dosage_form' => 'capsule',
                'strength' => '500',
                'unit' => 'mg',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.medications.index'));
        $this->assertDatabaseHas('medications', ['generic_name' => 'Amoxicillin', 'brand_name' => 'Amoxil', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_medication(): void
    {
        $medication = Medication::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.medications.update', $medication), [
                'generic_name' => 'Updated Name',
            ])
            ->assertRedirect(route('admin.medications.index'));

        $this->assertDatabaseHas('medications', ['id' => $medication->id, 'generic_name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_medication(): void
    {
        $medication = Medication::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.medications.destroy', $medication))
            ->assertRedirect(route('admin.medications.index'));

        $this->assertSoftDeleted($medication);
    }
}
