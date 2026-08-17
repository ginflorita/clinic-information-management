<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MedicalHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_medical_history_uses_structured_columns_not_a_single_text_blob(): void
    {
        // The playbook explicitly forbids a single `medical_history TEXT` column —
        // assert each concept has its own queryable column instead.
        foreach ([
            'previous_surgeries',
            'hospitalization',
            'current_medications',
            'smoking_status',
            'alcohol_use',
            'family_medical_history',
            'physician_name',
            'medical_alerts',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('medical_histories', $column), "Missing structured column: {$column}");
        }
    }

    public function test_a_medical_history_can_be_recorded_for_a_patient(): void
    {
        $patient = Patient::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->put(route('patients.medical-history.update', $patient), [
                'physician_name' => 'Dr. Santos',
                'smoking_status' => 'non-smoker',
                'medical_alerts' => 'Bleeding disorder',
            ]);

        $response->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('medical_histories', [
            'patient_id' => $patient->id,
            'physician_name' => 'Dr. Santos',
            'medical_alerts' => 'Bleeding disorder',
            'recorded_by_user_id' => $user->id,
        ]);
    }

    public function test_updating_medical_history_again_updates_the_same_row(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->put(route('patients.medical-history.update', $patient), ['physician_name' => 'Dr. First']);

        $this->actingAs(User::factory()->create())
            ->put(route('patients.medical-history.update', $patient), ['physician_name' => 'Dr. Second']);

        $this->assertDatabaseCount('medical_histories', 1);
        $this->assertDatabaseHas('medical_histories', ['patient_id' => $patient->id, 'physician_name' => 'Dr. Second']);
    }
}
