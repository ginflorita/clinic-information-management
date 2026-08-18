<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Provider;
use App\Models\TreatmentPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreatmentPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_treatment_plans(): void
    {
        $this->get(route('treatment-plans.index'))->assertRedirect(route('login'));
    }

    public function test_a_treatment_plan_can_be_created(): void
    {
        $patient = Patient::factory()->create();
        $provider = Provider::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('treatment-plans.store'), [
                'patient_id' => $patient->id,
                'provider_id' => $provider->id,
                'title' => 'Full mouth rehabilitation',
            ]);

        $plan = TreatmentPlan::first();
        $response->assertRedirect(route('treatment-plans.show', $plan));

        $this->assertNotNull($plan);
        $this->assertSame('draft', $plan->status);
        $this->assertMatchesRegularExpression('/^TXP-\d{4}-\d{6}$/', $plan->plan_number);
    }

    public function test_a_plan_transitions_from_draft_through_presented_accepted_to_completed(): void
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'draft']);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('treatment-plans.transition', $plan), ['status' => 'presented']);
        $plan->refresh();
        $this->assertSame('presented', $plan->status);
        $this->assertNotNull($plan->presented_at);

        $this->actingAs($user)->post(route('treatment-plans.transition', $plan), ['status' => 'accepted']);
        $plan->refresh();
        $this->assertSame('accepted', $plan->status);
        $this->assertNotNull($plan->accepted_at);

        $this->actingAs($user)->post(route('treatment-plans.transition', $plan), ['status' => 'completed']);
        $plan->refresh();
        $this->assertSame('completed', $plan->status);
        $this->assertNotNull($plan->completed_at);
    }

    public function test_a_draft_plan_cannot_jump_directly_to_completed(): void
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'draft']);

        $this->actingAs(User::factory()->create())
            ->post(route('treatment-plans.transition', $plan), ['status' => 'completed'])
            ->assertSessionHasErrors('status');

        $this->assertSame('draft', $plan->fresh()->status);
    }

    public function test_a_presented_plan_can_be_declined(): void
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'presented']);

        $this->actingAs(User::factory()->create())
            ->post(route('treatment-plans.transition', $plan), ['status' => 'declined']);

        $this->assertSame('declined', $plan->fresh()->status);
    }

    public function test_a_draft_plan_can_be_cancelled(): void
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'draft']);

        $this->actingAs(User::factory()->create())
            ->post(route('treatment-plans.transition', $plan), ['status' => 'cancelled']);

        $this->assertSame('cancelled', $plan->fresh()->status);
    }

    public function test_a_declined_plan_cannot_transition_further(): void
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'declined']);

        $this->actingAs(User::factory()->create())
            ->post(route('treatment-plans.transition', $plan), ['status' => 'accepted'])
            ->assertSessionHasErrors('status');
    }

    public function test_treatment_plans_have_no_hard_delete_route(): void
    {
        $plan = TreatmentPlan::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->delete(route('treatment-plans.show', $plan));

        $response->assertStatus(405);
        $this->assertDatabaseHas('treatment_plans', ['id' => $plan->id]);
    }
}
