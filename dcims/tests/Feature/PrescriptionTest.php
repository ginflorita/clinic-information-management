<?php

namespace Tests\Feature;

use App\Models\Encounter;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_a_patient_prescription_history(): void
    {
        $patient = Patient::factory()->create();

        $this->get(route('patients.prescriptions.show', $patient))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_create_a_prescription(): void
    {
        $encounter = Encounter::factory()->create();

        $this->post(route('encounters.prescriptions.store', $encounter))->assertRedirect(route('login'));
    }

    public function test_a_prescription_can_be_created_for_an_encounter(): void
    {
        $encounter = Encounter::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.prescriptions.store', $encounter), [
                'notes' => 'Post-extraction pain management.',
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));

        $prescription = Prescription::first();
        $this->assertNotNull($prescription);
        $this->assertSame($encounter->id, $prescription->encounter_id);
        $this->assertSame($encounter->patient_id, $prescription->patient_id);
        $this->assertSame($encounter->provider_id, $prescription->provider_id);
        $this->assertSame('active', $prescription->status);
        $this->assertMatchesRegularExpression('/^RX-\d{4}-\d{6}$/', $prescription->prescription_number);
    }

    public function test_medications_can_be_added_to_an_active_prescription(): void
    {
        $encounter = Encounter::factory()->create();
        $prescription = Prescription::factory()->create(['encounter_id' => $encounter->id, 'patient_id' => $encounter->patient_id]);
        $medication = Medication::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.prescriptions.items.store', [$encounter, $prescription]), [
                'medication_id' => $medication->id,
                'dose' => '500mg',
                'frequency' => 'every 8 hours',
                'route' => 'oral',
                'duration' => '7 days',
                'quantity' => 21,
                'refills' => 1,
                'instructions' => 'Take with food.',
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));

        $item = $prescription->items()->first();
        $this->assertNotNull($item);
        $this->assertSame($medication->id, $item->medication_id);
        $this->assertSame('500mg', $item->dose);
        $this->assertSame(21, $item->quantity);
        $this->assertSame(1, $item->refills);
    }

    public function test_medications_cannot_be_added_to_a_cancelled_prescription(): void
    {
        $encounter = Encounter::factory()->create();
        $prescription = Prescription::factory()->create([
            'encounter_id' => $encounter->id,
            'patient_id' => $encounter->patient_id,
            'status' => 'cancelled',
        ]);
        $medication = Medication::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.prescriptions.items.store', [$encounter, $prescription]), [
                'medication_id' => $medication->id,
                'dose' => '500mg',
                'frequency' => 'once daily',
                'quantity' => 10,
            ])
            ->assertSessionHasErrors('medication_id');

        $this->assertSame(0, $prescription->items()->count());
    }

    public function test_a_prescription_can_be_cancelled(): void
    {
        $encounter = Encounter::factory()->create();
        $prescription = Prescription::factory()->create(['encounter_id' => $encounter->id, 'patient_id' => $encounter->patient_id]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.prescriptions.cancel', [$encounter, $prescription]));

        $response->assertRedirect(route('encounters.show', $encounter));
        $this->assertSame('cancelled', $prescription->fresh()->status);
    }

    public function test_a_cancelled_prescription_cannot_be_cancelled_again(): void
    {
        $encounter = Encounter::factory()->create();
        $prescription = Prescription::factory()->create([
            'encounter_id' => $encounter->id,
            'patient_id' => $encounter->patient_id,
            'status' => 'cancelled',
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.prescriptions.cancel', [$encounter, $prescription]))
            ->assertSessionHasErrors('status');
    }

    public function test_patient_prescription_history_shows_prescriptions_across_encounters(): void
    {
        $patient = Patient::factory()->create();
        $user = User::factory()->create();

        $visit1 = Encounter::factory()->create(['patient_id' => $patient->id]);
        Prescription::factory()->create(['encounter_id' => $visit1->id, 'patient_id' => $patient->id]);

        $visit2 = Encounter::factory()->create(['patient_id' => $patient->id]);
        Prescription::factory()->create(['encounter_id' => $visit2->id, 'patient_id' => $patient->id]);

        $response = $this->actingAs($user)->get(route('patients.prescriptions.show', $patient));

        $response->assertOk();
        $this->assertCount(2, $response->viewData('prescriptions'));
    }
}
