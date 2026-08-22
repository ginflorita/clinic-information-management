<?php

namespace Tests\Feature;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\PerioExamination;
use App\Models\Tooth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerioTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_a_patient_periodontal_chart(): void
    {
        $patient = Patient::factory()->create();

        $this->get(route('patients.periodontal.show', $patient))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_record_a_perio_measurement(): void
    {
        $encounter = Encounter::factory()->create();

        $this->post(route('encounters.perio-tooth-records.store', $encounter))->assertRedirect(route('login'));
    }

    public function test_recording_measurements_creates_the_examination_for_the_encounter(): void
    {
        $encounter = Encounter::factory()->create();
        $tooth = Tooth::factory()->create(['tooth_code' => '36']);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.perio-tooth-records.store', $encounter), [
                'tooth_id' => $tooth->id,
                'mobility' => 1,
                'furcation' => 0,
                'notes' => 'Slight bleeding on distal.',
                'sites' => [
                    'mesial' => ['probing_depth' => 3],
                    'mid' => ['probing_depth' => 2],
                    'distal' => ['probing_depth' => 4, 'bleeding_on_probing' => 1],
                ],
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));

        $examination = PerioExamination::first();
        $this->assertNotNull($examination);
        $this->assertSame($encounter->id, $examination->encounter_id);
        $this->assertSame($encounter->patient_id, $examination->patient_id);

        $record = $examination->toothRecords()->first();
        $this->assertSame($tooth->id, $record->tooth_id);
        $this->assertSame(1, $record->mobility);
        $this->assertSame('Slight bleeding on distal.', $record->notes);
        $this->assertSame(3, $record->measurements()->count());

        $distal = $record->measurements()->where('site', 'distal')->first();
        $this->assertSame('4.0', $distal->probing_depth);
        $this->assertTrue($distal->bleeding_on_probing);
    }

    public function test_sites_left_blank_are_not_recorded(): void
    {
        $encounter = Encounter::factory()->create();
        $tooth = Tooth::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.perio-tooth-records.store', $encounter), [
                'tooth_id' => $tooth->id,
                'sites' => [
                    'mesial' => ['probing_depth' => 3],
                    'mid' => ['probing_depth' => ''],
                    'distal' => ['probing_depth' => ''],
                ],
            ]);

        $record = PerioExamination::first()->toothRecords()->first();
        $this->assertSame(1, $record->measurements()->count());
    }

    public function test_at_least_one_site_measurement_is_required(): void
    {
        $encounter = Encounter::factory()->create();
        $tooth = Tooth::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.perio-tooth-records.store', $encounter), [
                'tooth_id' => $tooth->id,
                'sites' => [
                    'mesial' => ['probing_depth' => ''],
                ],
            ])
            ->assertSessionHasErrors('sites');
    }

    public function test_recording_a_second_submission_for_the_same_tooth_updates_it_rather_than_duplicating(): void
    {
        $encounter = Encounter::factory()->create();
        $tooth = Tooth::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('encounters.perio-tooth-records.store', $encounter), [
            'tooth_id' => $tooth->id,
            'mobility' => 1,
            'sites' => ['mesial' => ['probing_depth' => 3]],
        ]);

        $this->actingAs($user)->post(route('encounters.perio-tooth-records.store', $encounter), [
            'tooth_id' => $tooth->id,
            'mobility' => 2,
            'sites' => ['mesial' => ['probing_depth' => 5]],
        ]);

        $this->assertSame(1, PerioExamination::count());
        $record = PerioExamination::first()->toothRecords()->first();
        $this->assertSame(2, $record->mobility);
        $this->assertSame(1, $record->measurements()->count());
        $this->assertSame('5.0', $record->measurements()->first()->probing_depth);
    }

    public function test_chart_history_is_preserved_across_visits_not_overwritten(): void
    {
        $patient = Patient::factory()->create();
        $tooth = Tooth::factory()->create(['tooth_code' => '36']);
        $user = User::factory()->create();

        $visit1 = Encounter::factory()->create(['patient_id' => $patient->id]);
        $this->actingAs($user)->post(route('encounters.perio-tooth-records.store', $visit1), [
            'tooth_id' => $tooth->id,
            'sites' => ['mesial' => ['probing_depth' => 3]],
        ]);

        $visit2 = Encounter::factory()->create(['patient_id' => $patient->id]);
        $this->actingAs($user)->post(route('encounters.perio-tooth-records.store', $visit2), [
            'tooth_id' => $tooth->id,
            'sites' => ['mesial' => ['probing_depth' => 6]],
        ]);

        $this->assertSame(2, PerioExamination::count());
        $this->assertDatabaseHas('perio_site_measurements', ['probing_depth' => 3]);
        $this->assertDatabaseHas('perio_site_measurements', ['probing_depth' => 6]);

        $response = $this->actingAs($user)->get(route('patients.periodontal.show', $patient));

        $response->assertOk();
    }
}
