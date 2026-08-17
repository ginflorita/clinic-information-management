<?php

namespace Tests\Feature;

use App\Models\Encounter;
use App\Models\Odontogram;
use App\Models\Patient;
use App\Models\Tooth;
use App\Models\ToothCondition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OdontogramTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_a_patient_chart(): void
    {
        $patient = Patient::factory()->create();

        $this->get(route('patients.odontogram.show', $patient))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_record_a_chart_entry(): void
    {
        $encounter = Encounter::factory()->create();

        $this->post(route('encounters.odontogram-entries.store', $encounter))->assertRedirect(route('login'));
    }

    public function test_recording_a_chart_entry_creates_the_odontogram_for_the_encounter(): void
    {
        $encounter = Encounter::factory()->create();
        $tooth = Tooth::factory()->create(['tooth_code' => '36']);
        $condition = ToothCondition::factory()->create(['name' => 'Caries']);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.odontogram-entries.store', $encounter), [
                'tooth_id' => $tooth->id,
                'condition_id' => $condition->id,
                'surfaces' => ['M', 'O'],
                'notes' => 'Deep caries on mesial-occlusal.',
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));

        $odontogram = Odontogram::first();
        $this->assertNotNull($odontogram);
        $this->assertSame($encounter->id, $odontogram->encounter_id);
        $this->assertSame($encounter->patient_id, $odontogram->patient_id);

        $entry = $odontogram->entries()->first();
        $this->assertSame($tooth->id, $entry->tooth_id);
        $this->assertSame($condition->id, $entry->condition_id);
        $this->assertSame('Deep caries on mesial-occlusal.', $entry->notes);
        $this->assertEqualsCanonicalizing(['M', 'O'], $entry->surfaces->pluck('surface')->all());
    }

    public function test_recording_a_second_entry_on_the_same_encounter_reuses_the_existing_odontogram(): void
    {
        $encounter = Encounter::factory()->create();
        $condition = ToothCondition::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('encounters.odontogram-entries.store', $encounter), [
            'tooth_id' => Tooth::factory()->create()->id,
            'condition_id' => $condition->id,
        ]);
        $this->actingAs($user)->post(route('encounters.odontogram-entries.store', $encounter), [
            'tooth_id' => Tooth::factory()->create()->id,
            'condition_id' => $condition->id,
        ]);

        $this->assertSame(1, Odontogram::count());
        $this->assertSame(2, Odontogram::first()->entries()->count());
    }

    public function test_an_invalid_surface_is_rejected(): void
    {
        $encounter = Encounter::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.odontogram-entries.store', $encounter), [
                'tooth_id' => Tooth::factory()->create()->id,
                'condition_id' => ToothCondition::factory()->create()->id,
                'surfaces' => ['X'],
            ])
            ->assertSessionHasErrors('surfaces.0');
    }

    public function test_chart_history_is_preserved_across_visits_not_overwritten(): void
    {
        $patient = Patient::factory()->create();
        $tooth = Tooth::factory()->create(['tooth_code' => '36']);
        $caries = ToothCondition::factory()->create(['name' => 'Caries']);
        $restored = ToothCondition::factory()->create(['name' => 'Restored']);
        $user = User::factory()->create();

        $visit1 = Encounter::factory()->create(['patient_id' => $patient->id]);
        $this->actingAs($user)->post(route('encounters.odontogram-entries.store', $visit1), [
            'tooth_id' => $tooth->id,
            'condition_id' => $caries->id,
        ]);

        $visit2 = Encounter::factory()->create(['patient_id' => $patient->id]);
        $this->actingAs($user)->post(route('encounters.odontogram-entries.store', $visit2), [
            'tooth_id' => $tooth->id,
            'condition_id' => $restored->id,
        ]);

        $this->assertSame(2, Odontogram::count());
        $this->assertDatabaseHas('odontogram_entries', ['tooth_id' => $tooth->id, 'condition_id' => $caries->id]);
        $this->assertDatabaseHas('odontogram_entries', ['tooth_id' => $tooth->id, 'condition_id' => $restored->id]);

        $response = $this->actingAs($user)->get(route('patients.odontogram.show', $patient));

        $response->assertOk();
        $response->assertSeeInOrder(['Caries', 'Restored']);
    }
}
