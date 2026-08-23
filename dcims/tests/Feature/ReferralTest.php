<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Provider;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_the_referrals_list(): void
    {
        $this->get(route('referrals.index'))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_create_a_referral(): void
    {
        $patient = Patient::factory()->create();

        $this->post(route('patients.referrals.store', $patient))->assertRedirect(route('login'));
    }

    public function test_a_referral_can_be_created_for_a_patient(): void
    {
        $patient = Patient::factory()->create();
        $provider = Provider::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('patients.referrals.store', $patient), [
                'referring_provider_id' => $provider->id,
                'receiving_name' => 'Dr. Jane Smith',
                'receiving_specialty' => 'Oral Surgery',
                'receiving_contact' => '555-0100',
                'reason' => 'Impacted third molar extraction.',
                'referral_date' => now()->toDateString(),
            ]);

        $response->assertRedirect(route('patients.show', $patient));

        $referral = Referral::first();
        $this->assertNotNull($referral);
        $this->assertSame($patient->id, $referral->patient_id);
        $this->assertSame($provider->id, $referral->referring_provider_id);
        $this->assertSame('draft', $referral->status);
        $this->assertMatchesRegularExpression('/^REF-\d{4}-\d{6}$/', $referral->referral_number);
    }

    public function test_a_referral_moves_through_the_full_status_sequence(): void
    {
        $referral = Referral::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('referrals.transition', $referral), ['status' => 'sent']);
        $this->assertSame('sent', $referral->fresh()->status);

        $this->actingAs($user)->post(route('referrals.transition', $referral), [
            'status' => 'received',
            'response' => 'Patient seen, surgery scheduled.',
        ]);
        $referral->refresh();
        $this->assertSame('received', $referral->status);
        $this->assertSame('Patient seen, surgery scheduled.', $referral->response);

        $this->actingAs($user)->post(route('referrals.transition', $referral), ['status' => 'completed']);
        $this->assertSame('completed', $referral->fresh()->status);
    }

    public function test_a_draft_referral_cannot_jump_directly_to_completed(): void
    {
        $referral = Referral::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('referrals.transition', $referral), ['status' => 'completed'])
            ->assertSessionHasErrors('status');

        $this->assertSame('draft', $referral->fresh()->status);
    }

    public function test_a_draft_referral_can_be_cancelled(): void
    {
        $referral = Referral::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('referrals.transition', $referral), ['status' => 'cancelled'])
            ->assertRedirect(route('referrals.index'));

        $this->assertSame('cancelled', $referral->fresh()->status);
    }

    public function test_a_cancelled_referral_cannot_transition_further(): void
    {
        $referral = Referral::factory()->create(['status' => 'cancelled']);

        $this->actingAs(User::factory()->create())
            ->post(route('referrals.transition', $referral), ['status' => 'sent'])
            ->assertSessionHasErrors('status');
    }

    public function test_the_referrals_list_shows_referrals_across_patients(): void
    {
        Referral::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->create())->get(route('referrals.index'));

        $response->assertOk();
        $this->assertCount(3, $response->viewData('referrals'));
    }
}
