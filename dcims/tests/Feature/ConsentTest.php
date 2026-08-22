<?php

namespace Tests\Feature;

use App\Models\Consent;
use App\Models\ConsentType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_record_a_consent(): void
    {
        $patient = Patient::factory()->create();

        $this->post(route('patients.consents.store', $patient))->assertRedirect(route('login'));
    }

    public function test_a_consent_can_be_recorded_for_a_patient(): void
    {
        $patient = Patient::factory()->create();
        $consentType = ConsentType::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('patients.consents.store', $patient), [
                'consent_type_id' => $consentType->id,
                'notes' => 'Signed on paper form.',
            ]);

        $response->assertRedirect(route('patients.show', $patient));

        $consent = Consent::first();
        $this->assertNotNull($consent);
        $this->assertSame($patient->id, $consent->patient_id);
        $this->assertSame($consentType->id, $consent->consent_type_id);
        $this->assertSame($user->id, $consent->obtained_by);
        $this->assertSame('granted', $consent->status);
        $this->assertNotNull($consent->granted_at);
    }

    public function test_a_granted_consent_can_be_revoked(): void
    {
        $patient = Patient::factory()->create();
        $consent = Consent::factory()->create(['patient_id' => $patient->id]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('patients.consents.revoke', [$patient, $consent]));

        $response->assertRedirect(route('patients.show', $patient));
        $consent->refresh();
        $this->assertSame('revoked', $consent->status);
        $this->assertNotNull($consent->revoked_at);
    }

    public function test_a_revoked_consent_cannot_be_revoked_again(): void
    {
        $patient = Patient::factory()->create();
        $consent = Consent::factory()->create([
            'patient_id' => $patient->id,
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('patients.consents.revoke', [$patient, $consent]))
            ->assertSessionHasErrors('status');
    }
}
