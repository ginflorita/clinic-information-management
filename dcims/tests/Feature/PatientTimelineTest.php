<?php

namespace Tests\Feature;

use App\Models\Diagnosis;
use App\Models\Encounter;
use App\Models\EncounterDiagnosis;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Procedure;
use App\Models\ProcedureRecord;
use App\Models\TreatmentPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_the_timeline(): void
    {
        $patient = Patient::factory()->create();

        $this->get(route('patients.timeline.show', $patient))->assertRedirect(route('login'));
    }

    public function test_the_timeline_includes_patient_registration(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs(User::factory()->create())->get(route('patients.timeline.show', $patient));

        $response->assertOk();
        $response->assertSee('Patient registered');
    }

    public function test_the_timeline_includes_an_encounter(): void
    {
        $patient = Patient::factory()->create();
        Encounter::factory()->create(['patient_id' => $patient->id, 'chief_complaint' => 'Toothache']);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.timeline.show', $patient));

        $response->assertSee('Encounter: Toothache');
    }

    public function test_the_timeline_includes_a_diagnosis(): void
    {
        $patient = Patient::factory()->create();
        $encounter = Encounter::factory()->create(['patient_id' => $patient->id]);
        $diagnosis = Diagnosis::factory()->create(['name' => 'Gingivitis']);
        EncounterDiagnosis::factory()->create([
            'encounter_id' => $encounter->id,
            'patient_id' => $patient->id,
            'diagnosis_id' => $diagnosis->id,
        ]);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.timeline.show', $patient));

        $response->assertSee('Diagnosis added: Gingivitis');
    }

    public function test_the_timeline_includes_treatment_plan_creation_and_acceptance(): void
    {
        $patient = Patient::factory()->create();
        TreatmentPlan::factory()->create([
            'patient_id' => $patient->id,
            'title' => 'Full mouth rehab',
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.timeline.show', $patient));

        $response->assertSee('Treatment plan created: Full mouth rehab');
        $response->assertSee('Treatment plan accepted: Full mouth rehab');
    }

    public function test_the_timeline_includes_a_completed_procedure(): void
    {
        $patient = Patient::factory()->create();
        $procedure = Procedure::factory()->create(['name' => 'Composite Filling']);
        ProcedureRecord::factory()->create([
            'patient_id' => $patient->id,
            'procedure_id' => $procedure->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.timeline.show', $patient));

        $response->assertSee('Procedure performed: Composite Filling');
    }

    public function test_the_timeline_includes_a_completed_payment(): void
    {
        $patient = Patient::factory()->create();
        Payment::factory()->create(['patient_id' => $patient->id, 'status' => 'completed', 'amount' => 500]);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.timeline.show', $patient));

        $response->assertSee('Payment received: 500.00');
    }

    public function test_events_are_returned_in_chronological_order_regardless_of_insertion_order(): void
    {
        $patient = Patient::factory()->create(['created_at' => '2026-08-01 08:00:00']);

        // Inserted out of chronological order on purpose.
        $procedure = Procedure::factory()->create();
        ProcedureRecord::factory()->create([
            'patient_id' => $patient->id,
            'procedure_id' => $procedure->id,
            'status' => 'completed',
            'performed_at' => '2026-08-13 10:00:00',
        ]);
        Payment::factory()->create([
            'patient_id' => $patient->id,
            'status' => 'completed',
            'payment_date' => '2026-08-05',
        ]);
        Encounter::factory()->create([
            'patient_id' => $patient->id,
            'started_at' => '2026-08-03 09:00:00',
        ]);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.timeline.show', $patient));

        $response->assertSeeInOrder(['Patient registered', 'Encounter:', 'Payment received', 'Procedure performed']);
    }

    public function test_the_timeline_is_scoped_to_the_patient(): void
    {
        $patient = Patient::factory()->create();
        $otherPatient = Patient::factory()->create();
        Encounter::factory()->create(['patient_id' => $otherPatient->id, 'chief_complaint' => 'Someone else\'s visit']);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.timeline.show', $patient));

        $response->assertDontSee('Someone else\'s visit');
    }
}
