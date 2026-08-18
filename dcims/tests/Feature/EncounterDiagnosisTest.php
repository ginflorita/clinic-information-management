<?php

namespace Tests\Feature;

use App\Models\Diagnosis;
use App\Models\Encounter;
use App\Models\EncounterDiagnosis;
use App\Models\Patient;
use App\Models\Tooth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EncounterDiagnosisTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_record_a_diagnosis(): void
    {
        $encounter = Encounter::factory()->create();

        $this->post(route('encounters.diagnoses.store', $encounter))->assertRedirect(route('login'));
    }

    public function test_a_diagnosis_can_be_attached_to_an_encounter(): void
    {
        $encounter = Encounter::factory()->create();
        $diagnosis = Diagnosis::factory()->create(['name' => 'Dental caries']);
        $tooth = Tooth::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.diagnoses.store', $encounter), [
                'diagnosis_id' => $diagnosis->id,
                'tooth_id' => $tooth->id,
                'notes' => 'Distal surface, tooth 36.',
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));

        $this->assertDatabaseHas('encounter_diagnoses', [
            'encounter_id' => $encounter->id,
            'patient_id' => $encounter->patient_id,
            'diagnosis_id' => $diagnosis->id,
            'tooth_id' => $tooth->id,
            'status' => 'active',
            'notes' => 'Distal surface, tooth 36.',
        ]);
    }

    public function test_a_diagnosis_status_can_be_updated(): void
    {
        $encounter = Encounter::factory()->create();
        $encounterDiagnosis = EncounterDiagnosis::factory()->create([
            'encounter_id' => $encounter->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->patch(route('encounters.diagnoses.status', [$encounter, $encounterDiagnosis]), [
                'status' => 'resolved',
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));
        $this->assertSame('resolved', $encounterDiagnosis->fresh()->status);
    }

    public function test_an_invalid_status_is_rejected(): void
    {
        $encounter = Encounter::factory()->create();
        $encounterDiagnosis = EncounterDiagnosis::factory()->create(['encounter_id' => $encounter->id]);

        $this->actingAs(User::factory()->create())
            ->patch(route('encounters.diagnoses.status', [$encounter, $encounterDiagnosis]), [
                'status' => 'cured',
            ])
            ->assertSessionHasErrors('status');
    }

    public function test_a_diagnosis_is_retrievable_from_the_patient_timeline(): void
    {
        $patient = Patient::factory()->create();
        $encounter = Encounter::factory()->create(['patient_id' => $patient->id]);
        $diagnosis = Diagnosis::factory()->create(['name' => 'Gingivitis']);

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.diagnoses.store', $encounter), ['diagnosis_id' => $diagnosis->id]);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.show', $patient));

        $response->assertOk();
        $response->assertSee('Gingivitis');
    }
}
