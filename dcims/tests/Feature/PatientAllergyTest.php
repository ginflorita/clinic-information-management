<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\PatientAllergy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PatientAllergyTest extends TestCase
{
    use RefreshDatabase;

    public function test_allergy_severity_and_reaction_are_distinct_structured_columns(): void
    {
        foreach (['allergen', 'reaction', 'severity', 'onset_date', 'notes', 'status'] as $column) {
            $this->assertTrue(Schema::hasColumn('patient_allergies', $column), "Missing structured column: {$column}");
        }
    }

    public function test_an_allergy_can_be_added_to_a_patient(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.allergies.store', $patient), [
                'allergen' => 'Penicillin',
                'reaction' => 'Anaphylaxis',
                'severity' => 'severe',
            ])
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patient_allergies', [
            'patient_id' => $patient->id,
            'allergen' => 'Penicillin',
            'reaction' => 'Anaphylaxis',
            'severity' => 'severe',
            'status' => 'active',
        ]);
    }

    public function test_severity_must_be_a_recognized_value(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.allergies.store', $patient), [
                'allergen' => 'Latex',
                'severity' => 'catastrophic',
            ])
            ->assertSessionHasErrors('severity');
    }

    public function test_active_allergies_render_prominently_at_the_top_of_the_patient_profile(): void
    {
        $patient = Patient::factory()->create();
        PatientAllergy::factory()->create([
            'patient_id' => $patient->id,
            'allergen' => 'Penicillin',
            'reaction' => 'Anaphylaxis',
            'severity' => 'severe',
            'status' => 'active',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('patients.show', $patient));

        $response->assertOk();
        $response->assertSeeText('Allergies');
        $response->assertSeeText('Penicillin');
        $response->assertSeeText('severe');
        $response->assertSeeText('Anaphylaxis');

        // The allergy banner must appear before the basic patient info card,
        // not buried further down the page or in a separate tab.
        $content = $response->getContent();
        $allergyBannerPos = strpos($content, 'Allergies:');
        $basicInfoPos = strpos($content, 'Registration Date');

        $this->assertNotFalse($allergyBannerPos);
        $this->assertNotFalse($basicInfoPos);
        $this->assertLessThan($basicInfoPos, $allergyBannerPos);
    }

    public function test_inactive_allergies_do_not_appear_in_the_prominent_banner(): void
    {
        $patient = Patient::factory()->create();
        PatientAllergy::factory()->create([
            'patient_id' => $patient->id,
            'allergen' => 'Old Resolved Allergy',
            'status' => 'inactive',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('patients.show', $patient));

        $response->assertOk();
        $response->assertDontSeeText('Allergies:');
    }

    public function test_an_allergy_can_be_removed(): void
    {
        $patient = Patient::factory()->create();
        $allergy = PatientAllergy::factory()->create(['patient_id' => $patient->id]);

        $this->actingAs(User::factory()->create())
            ->delete(route('patients.allergies.destroy', [$patient, $allergy]))
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseMissing('patient_allergies', ['id' => $allergy->id]);
    }
}
