<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Recall;
use App\Models\RecallType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecallTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_the_recalls_list(): void
    {
        $this->get(route('recalls.index'))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_schedule_a_recall(): void
    {
        $patient = Patient::factory()->create();

        $this->post(route('patients.recalls.store', $patient))->assertRedirect(route('login'));
    }

    public function test_a_recall_can_be_scheduled_for_a_patient(): void
    {
        $patient = Patient::factory()->create();
        $recallType = RecallType::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('patients.recalls.store', $patient), [
                'recall_type_id' => $recallType->id,
                'due_date' => now()->addMonths(6)->toDateString(),
                'notes' => 'Routine cleaning.',
            ]);

        $response->assertRedirect(route('patients.show', $patient));

        $recall = Recall::first();
        $this->assertNotNull($recall);
        $this->assertSame($patient->id, $recall->patient_id);
        $this->assertSame($recallType->id, $recall->recall_type_id);
        $this->assertSame('pending', $recall->status);
    }

    public function test_a_recall_can_be_marked_completed(): void
    {
        $recall = Recall::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('recalls.complete', $recall));

        $response->assertRedirect(route('recalls.index'));
        $recall->refresh();
        $this->assertSame('completed', $recall->status);
        $this->assertNotNull($recall->completed_date);
    }

    public function test_a_recall_can_be_cancelled(): void
    {
        $recall = Recall::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('recalls.cancel', $recall))
            ->assertRedirect(route('recalls.index'));

        $this->assertSame('cancelled', $recall->fresh()->status);
    }

    public function test_a_completed_recall_cannot_be_completed_again(): void
    {
        $recall = Recall::factory()->create(['status' => 'completed', 'completed_date' => now()->toDateString()]);

        $this->actingAs(User::factory()->create())
            ->post(route('recalls.complete', $recall))
            ->assertSessionHasErrors('status');
    }

    public function test_an_overdue_pending_recall_is_flagged_as_overdue(): void
    {
        $recall = Recall::factory()->create(['due_date' => now()->subDay()->toDateString(), 'status' => 'pending']);
        $notOverdue = Recall::factory()->create(['due_date' => now()->addMonth()->toDateString(), 'status' => 'pending']);

        $this->assertTrue($recall->isOverdue());
        $this->assertFalse($notOverdue->isOverdue());
    }

    public function test_the_recalls_list_shows_all_patients_recalls(): void
    {
        Recall::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->create())->get(route('recalls.index'));

        $response->assertOk();
        $this->assertCount(3, $response->viewData('recalls'));
    }
}
