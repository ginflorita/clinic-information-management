<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\QueueEntry;
use App\Models\TreatmentPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_the_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_todays_appointments_count_is_scoped_to_today(): void
    {
        Appointment::factory()->create(['scheduled_start' => today()->setHour(9), 'scheduled_end' => today()->setHour(10)]);
        Appointment::factory()->create(['scheduled_start' => today()->addDay()->setHour(9), 'scheduled_end' => today()->addDay()->setHour(10)]);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('metrics', fn ($metrics) => $metrics['todays_appointments'] === 1);
    }

    public function test_todays_patients_counts_distinct_patients(): void
    {
        $patient = Patient::factory()->create();
        Appointment::factory()->count(2)->create(['patient_id' => $patient->id, 'scheduled_start' => today()->setHour(9), 'scheduled_end' => today()->setHour(10)]);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertViewHas('metrics', fn ($metrics) => $metrics['todays_patients'] === 1);
    }

    public function test_waiting_and_currently_treating_counts(): void
    {
        QueueEntry::factory()->create(['queue_date' => today(), 'status' => 'waiting']);
        QueueEntry::factory()->create(['queue_date' => today(), 'status' => 'waiting']);
        QueueEntry::factory()->create(['queue_date' => today(), 'status' => 'in_treatment']);
        QueueEntry::factory()->create(['queue_date' => today(), 'status' => 'completed']);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertViewHas('metrics', function ($metrics) {
            return $metrics['waiting_patients'] === 2 && $metrics['currently_treating'] === 1;
        });
    }

    public function test_completed_appointment_is_derived_from_its_encounters_status(): void
    {
        $appointment = Appointment::factory()->create(['scheduled_start' => today()->setHour(9), 'scheduled_end' => today()->setHour(10)]);
        Encounter::factory()->create(['appointment_id' => $appointment->id, 'status' => 'completed']);
        Appointment::factory()->create(['scheduled_start' => today()->setHour(11), 'scheduled_end' => today()->setHour(12)]);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertViewHas('metrics', fn ($metrics) => $metrics['completed_appointments'] === 1);
    }

    public function test_cancelled_and_no_show_counts(): void
    {
        Appointment::factory()->create(['scheduled_start' => today()->setHour(9), 'scheduled_end' => today()->setHour(10), 'status' => 'cancelled']);
        Appointment::factory()->create(['scheduled_start' => today()->setHour(11), 'scheduled_end' => today()->setHour(12), 'status' => 'no_show']);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertViewHas('metrics', function ($metrics) {
            return $metrics['cancelled_appointments'] === 1 && $metrics['no_show_appointments'] === 1;
        });
    }

    public function test_todays_revenue_sums_only_completed_payments_from_today(): void
    {
        Payment::factory()->create(['payment_date' => today(), 'status' => 'completed', 'amount' => 500]);
        Payment::factory()->create(['payment_date' => today(), 'status' => 'completed', 'amount' => 300]);
        Payment::factory()->create(['payment_date' => today(), 'status' => 'voided', 'amount' => 999]);
        Payment::factory()->create(['payment_date' => today()->subDay(), 'status' => 'completed', 'amount' => 999]);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertViewHas('metrics', fn ($metrics) => (float) $metrics['todays_revenue'] === 800.0);
    }

    public function test_outstanding_balances_sums_issued_invoice_balances(): void
    {
        Invoice::factory()->create(['status' => 'issued', 'total_amount' => 1000]);
        Invoice::factory()->create(['status' => 'issued', 'total_amount' => 500]);
        Invoice::factory()->create(['status' => 'cancelled', 'total_amount' => 999]);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertViewHas('metrics', fn ($metrics) => (float) $metrics['outstanding_balances'] === 1500.0);
    }

    public function test_new_patients_counts_todays_registrations(): void
    {
        Patient::factory()->create();
        Patient::factory()->create(['created_at' => today()->subDay()]);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertViewHas('metrics', fn ($metrics) => $metrics['new_patients'] === 1);
    }

    public function test_pending_treatment_plans_counts_draft_and_presented_only(): void
    {
        TreatmentPlan::factory()->create(['status' => 'draft']);
        TreatmentPlan::factory()->create(['status' => 'presented']);
        TreatmentPlan::factory()->create(['status' => 'accepted']);
        TreatmentPlan::factory()->create(['status' => 'completed']);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertViewHas('metrics', fn ($metrics) => $metrics['pending_treatment_plans'] === 2);
    }

    public function test_follow_up_patients_matches_appointment_type_named_follow_up(): void
    {
        $followUpType = AppointmentType::factory()->create(['name' => 'Follow-up']);
        $cleaningType = AppointmentType::factory()->create(['name' => 'Cleaning']);

        Appointment::factory()->create(['appointment_type_id' => $followUpType->id, 'scheduled_start' => today()->setHour(9), 'scheduled_end' => today()->setHour(10)]);
        Appointment::factory()->create(['appointment_type_id' => $cleaningType->id, 'scheduled_start' => today()->setHour(11), 'scheduled_end' => today()->setHour(12)]);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertViewHas('metrics', fn ($metrics) => $metrics['follow_up_patients'] === 1);
    }

    public function test_low_stock_and_expiring_inventory_are_stubbed_to_zero(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertViewHas('metrics', function ($metrics) {
            return $metrics['low_stock_items'] === 0 && $metrics['expiring_inventory'] === 0;
        });
    }

    public function test_recent_activity_includes_a_newly_registered_patient(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Patient registered: '.$patient->full_name);
    }
}
