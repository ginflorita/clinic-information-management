<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\PatientRelationship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guardian_who_is_not_a_patient_can_be_recorded(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('patients.relationships.store', $patient), [
                'contact_name' => 'Juan Dela Cruz',
                'contact_phone' => '0917xxxxxxx',
                'relationship_type' => 'father',
                'is_guardian' => '1',
                'is_emergency_contact' => '1',
            ]);

        $response->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patient_relationships', [
            'patient_id' => $patient->id,
            'related_patient_id' => null,
            'contact_name' => 'Juan Dela Cruz',
            'is_guardian' => true,
        ]);
    }

    public function test_a_relationship_can_link_to_an_existing_patient(): void
    {
        $patient = Patient::factory()->create();
        $mother = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.relationships.store', $patient), [
                'related_patient_id' => $mother->id,
                'relationship_type' => 'mother',
                'is_guardian' => '1',
            ])
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patient_relationships', [
            'patient_id' => $patient->id,
            'related_patient_id' => $mother->id,
            'contact_name' => null,
        ]);
    }

    public function test_relationship_requires_exactly_one_of_related_patient_or_contact_name(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.relationships.store', $patient), [
                'relationship_type' => 'father',
            ])
            ->assertSessionHasErrors('contact_name');

        $this->assertDatabaseCount('patient_relationships', 0);
    }

    public function test_relationship_rejects_both_related_patient_and_contact_name(): void
    {
        $patient = Patient::factory()->create();
        $other = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.relationships.store', $patient), [
                'related_patient_id' => $other->id,
                'contact_name' => 'Should Not Coexist',
                'relationship_type' => 'father',
            ])
            ->assertSessionHasErrors('contact_name');

        $this->assertDatabaseCount('patient_relationships', 0);
    }

    public function test_a_relationship_can_be_removed(): void
    {
        $patient = Patient::factory()->create();
        $relationship = PatientRelationship::factory()->create(['patient_id' => $patient->id]);

        $this->actingAs(User::factory()->create())
            ->delete(route('patients.relationships.destroy', [$patient, $relationship]))
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseMissing('patient_relationships', ['id' => $relationship->id]);
    }
}
