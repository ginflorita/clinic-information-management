<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\PatientCondition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PatientConditionTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_conditions_use_structured_columns(): void
    {
        foreach (['condition_name', 'status', 'diagnosed_date', 'notes'] as $column) {
            $this->assertTrue(Schema::hasColumn('patient_conditions', $column), "Missing structured column: {$column}");
        }
    }

    public function test_a_condition_can_be_added_to_a_patient(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.conditions.store', $patient), [
                'condition_name' => 'Diabetes',
                'status' => 'managed',
                'diagnosed_date' => '2020-01-01',
            ])
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patient_conditions', [
            'patient_id' => $patient->id,
            'condition_name' => 'Diabetes',
            'status' => 'managed',
        ]);
    }

    public function test_multiple_conditions_are_stored_as_separate_rows_not_one_blob(): void
    {
        $patient = Patient::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('patients.conditions.store', $patient), [
            'condition_name' => 'Diabetes',
            'status' => 'active',
        ]);
        $this->actingAs($user)->post(route('patients.conditions.store', $patient), [
            'condition_name' => 'Hypertension',
            'status' => 'active',
        ]);

        $this->assertSame(2, PatientCondition::where('patient_id', $patient->id)->count());
        $this->assertSame(
            ['Diabetes', 'Hypertension'],
            PatientCondition::where('patient_id', $patient->id)->orderBy('id')->pluck('condition_name')->all()
        );
    }

    public function test_a_condition_can_be_removed(): void
    {
        $patient = Patient::factory()->create();
        $condition = PatientCondition::factory()->create(['patient_id' => $patient->id]);

        $this->actingAs(User::factory()->create())
            ->delete(route('patients.conditions.destroy', [$patient, $condition]))
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseMissing('patient_conditions', ['id' => $condition->id]);
    }
}
