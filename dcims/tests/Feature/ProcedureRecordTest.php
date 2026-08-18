<?php

namespace Tests\Feature;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\ProcedureRecord;
use App\Models\Tooth;
use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcedureRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_record_a_procedure(): void
    {
        $encounter = Encounter::factory()->create();

        $this->post(route('encounters.procedure-records.store', $encounter))->assertRedirect(route('login'));
    }

    public function test_a_procedure_can_be_recorded_against_an_encounter_and_tooth(): void
    {
        $encounter = Encounter::factory()->create();
        $procedure = Procedure::factory()->create(['default_fee' => 2500]);
        $tooth = Tooth::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.procedure-records.store', $encounter), [
                'procedure_id' => $procedure->id,
                'tooth_id' => $tooth->id,
                'quantity' => 1,
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));

        $record = ProcedureRecord::first();
        $this->assertSame($encounter->id, $record->encounter_id);
        $this->assertSame($encounter->patient_id, $record->patient_id);
        $this->assertSame($encounter->provider_id, $record->provider_id);
        $this->assertSame($tooth->id, $record->tooth_id);
        $this->assertEquals(2500, $record->unit_price);
        $this->assertEquals(2500, $record->total_amount);
        $this->assertSame('completed', $record->status);
        $this->assertNull($record->treatment_plan_item_id);
    }

    public function test_completing_a_procedure_record_from_a_treatment_plan_item_keeps_them_as_separate_rows(): void
    {
        $patient = Patient::factory()->create();
        $encounter = Encounter::factory()->create(['patient_id' => $patient->id]);
        $plan = TreatmentPlan::factory()->create(['patient_id' => $patient->id]);
        $item = TreatmentPlanItem::factory()->create([
            'treatment_plan_id' => $plan->id,
            'status' => 'accepted',
            'quantity' => 1,
            'estimated_unit_price' => 15000,
            'estimated_total' => 15000,
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.procedure-records.store', $encounter), [
                'procedure_id' => $item->procedure_id,
                'treatment_plan_item_id' => $item->id,
                'quantity' => 1,
                'unit_price' => 15000,
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));

        $this->assertDatabaseCount('treatment_plan_items', 1);
        $this->assertDatabaseCount('procedure_records', 1);

        $item->refresh();
        $this->assertSame('completed', $item->status);
        $this->assertEquals(15000, $item->estimated_unit_price, 'The plan item must retain its own estimated data, not be overwritten by the actual record.');

        $record = ProcedureRecord::first();
        $this->assertSame($item->id, $record->treatment_plan_item_id);
    }

    public function test_a_plan_item_from_a_different_patient_is_rejected(): void
    {
        $encounter = Encounter::factory()->create();
        $otherPatientItem = TreatmentPlanItem::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.procedure-records.store', $encounter), [
                'procedure_id' => $otherPatientItem->procedure_id,
                'treatment_plan_item_id' => $otherPatientItem->id,
                'quantity' => 1,
            ])
            ->assertSessionHasErrors('treatment_plan_item_id');
    }

    public function test_a_completed_record_can_be_voided(): void
    {
        $encounter = Encounter::factory()->create();
        $record = ProcedureRecord::factory()->create(['encounter_id' => $encounter->id, 'status' => 'completed']);

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.procedure-records.void', [$encounter, $record]));

        $this->assertSame('voided', $record->fresh()->status);
    }

    public function test_an_already_voided_record_cannot_be_voided_again(): void
    {
        $encounter = Encounter::factory()->create();
        $record = ProcedureRecord::factory()->create(['encounter_id' => $encounter->id, 'status' => 'voided']);

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.procedure-records.void', [$encounter, $record]))
            ->assertSessionHasErrors('status');
    }
}
