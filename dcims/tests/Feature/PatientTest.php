<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_patients(): void
    {
        $this->get(route('patients.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_register_a_patient(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('patients.store'), [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'date_of_birth' => '1990-05-15',
                'sex' => 'male',
                'registration_date' => now()->format('Y-m-d'),
            ]);

        $patient = Patient::first();

        $response->assertRedirect(route('patients.show', $patient));
        $this->assertNotNull($patient);
        $this->assertSame('Juan', $patient->first_name);
        $this->assertMatchesRegularExpression('/^PAT-\d{4}-\d{6}$/', $patient->patient_number);
        $this->assertSame('active', $patient->status);
    }

    public function test_patient_number_increments_sequentially(): void
    {
        $this->actingAs(User::factory()->create());

        $first = Patient::factory()->create();
        $second = Patient::factory()->create();

        $firstSeq = (int) substr($first->patient_number, -6);
        $secondSeq = (int) substr($second->patient_number, -6);

        $this->assertSame($firstSeq + 1, $secondSeq);
    }

    public function test_can_search_patients_by_name(): void
    {
        Patient::factory()->create(['first_name' => 'Maria', 'last_name' => 'Santos']);
        Patient::factory()->create(['first_name' => 'Pedro', 'last_name' => 'Reyes']);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('patients.index', ['q' => 'Santos']));

        $response->assertOk();
        $response->assertSee('Santos');
        $response->assertDontSee('Reyes');
    }

    public function test_can_search_patients_by_patient_number(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->get(route('patients.index', ['q' => $patient->patient_number]));

        $response->assertOk();
        $response->assertSee($patient->patient_number);
    }

    public function test_can_view_patient_profile(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('patients.show', $patient))
            ->assertOk()
            ->assertSee($patient->patient_number);
    }

    public function test_can_edit_a_patient(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->put(route('patients.update', $patient), [
                'first_name' => 'Updated',
                'last_name' => $patient->last_name,
                'date_of_birth' => $patient->date_of_birth->format('Y-m-d'),
                'sex' => $patient->sex,
                'registration_date' => $patient->registration_date->format('Y-m-d'),
            ]);

        $response->assertRedirect(route('patients.show', $patient));
        $this->assertSame('Updated', $patient->fresh()->first_name);
    }

    public function test_archiving_a_patient_does_not_delete_the_row(): void
    {
        $patient = Patient::factory()->create(['status' => 'active']);

        $this->actingAs(User::factory()->create())
            ->post(route('patients.archive', $patient))
            ->assertRedirect(route('patients.show', $patient));

        $patient->refresh();
        $this->assertSame('archived', $patient->status);
        $this->assertNotNull($patient->archived_at);
        $this->assertDatabaseHas('patients', ['id' => $patient->id]);
    }

    public function test_a_patient_can_be_restored_after_archiving(): void
    {
        $patient = Patient::factory()->create(['status' => 'archived', 'archived_at' => now()]);

        $this->actingAs(User::factory()->create())
            ->post(route('patients.restore', $patient))
            ->assertRedirect(route('patients.show', $patient));

        $patient->refresh();
        $this->assertSame('active', $patient->status);
        $this->assertNull($patient->archived_at);
    }

    public function test_patients_have_no_hard_delete_route(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->delete(route('patients.show', $patient));

        $response->assertStatus(405);
        $this->assertDatabaseHas('patients', ['id' => $patient->id]);
    }
}
