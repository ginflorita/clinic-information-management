<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EncounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_encounters(): void
    {
        $this->get(route('encounters.index'))->assertRedirect(route('login'));
    }

    public function test_an_encounter_can_be_started_from_an_appointment(): void
    {
        $appointment = Appointment::factory()->create(['reason' => 'Toothache']);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('appointments.encounter.start', $appointment));

        $encounter = Encounter::first();
        $response->assertRedirect(route('encounters.show', $encounter));

        $this->assertNotNull($encounter);
        $this->assertSame($appointment->id, $encounter->appointment_id);
        $this->assertSame($appointment->patient_id, $encounter->patient_id);
        $this->assertSame($appointment->provider_id, $encounter->provider_id);
        $this->assertSame('Toothache', $encounter->chief_complaint);
        $this->assertSame('in_progress', $encounter->status);
        $this->assertMatchesRegularExpression('/^ENC-\d{4}-\d{6}$/', $encounter->encounter_number);
    }

    public function test_starting_an_encounter_twice_for_the_same_appointment_reuses_the_existing_one(): void
    {
        $appointment = Appointment::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('appointments.encounter.start', $appointment));
        $this->actingAs($user)->post(route('appointments.encounter.start', $appointment));

        $this->assertSame(1, Encounter::count());
    }

    public function test_a_standalone_walk_in_encounter_can_be_created(): void
    {
        $patient = Patient::factory()->create();
        $provider = Provider::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.store'), [
                'patient_id' => $patient->id,
                'provider_id' => $provider->id,
                'chief_complaint' => 'Walk-in cleaning',
            ]);

        $encounter = Encounter::first();
        $response->assertRedirect(route('encounters.show', $encounter));
        $this->assertNull($encounter->appointment_id);
    }

    public function test_an_encounter_can_be_completed(): void
    {
        $encounter = Encounter::factory()->create(['status' => 'in_progress']);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.complete', $encounter));

        $response->assertRedirect(route('encounters.show', $encounter));

        $encounter->refresh();
        $this->assertSame('completed', $encounter->status);
        $this->assertNotNull($encounter->ended_at);
    }

    public function test_encounters_have_no_hard_delete_route(): void
    {
        $encounter = Encounter::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->delete(route('encounters.show', $encounter));

        $response->assertStatus(405);
        $this->assertDatabaseHas('encounters', ['id' => $encounter->id]);
    }
}
