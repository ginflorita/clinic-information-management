<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DentalHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dental_history_uses_structured_columns_not_a_single_text_blob(): void
    {
        foreach ([
            'previous_dentist',
            'previous_treatments',
            'previous_extraction',
            'previous_root_canal',
            'orthodontic_history',
            'dental_habits',
            'chief_concerns',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('dental_histories', $column), "Missing structured column: {$column}");
        }
    }

    public function test_a_dental_history_can_be_recorded_for_a_patient(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->put(route('patients.dental-history.update', $patient), [
                'previous_dentist' => 'Dr. Reyes',
                'previous_extraction' => '1',
                'chief_concerns' => 'Sensitivity on the lower left molar',
            ]);

        $response->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('dental_histories', [
            'patient_id' => $patient->id,
            'previous_dentist' => 'Dr. Reyes',
            'previous_extraction' => true,
            'previous_root_canal' => false,
            'chief_concerns' => 'Sensitivity on the lower left molar',
        ]);
    }
}
