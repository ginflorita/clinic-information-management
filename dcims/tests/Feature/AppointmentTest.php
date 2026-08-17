<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Chair;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_appointments(): void
    {
        $this->get(route('appointments.index'))->assertRedirect(route('login'));
    }

    public function test_an_appointment_can_be_created(): void
    {
        $patient = Patient::factory()->create();
        $provider = Provider::factory()->create();
        $type = AppointmentType::factory()->create();
        $chair = Chair::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('appointments.store'), [
                'patient_id' => $patient->id,
                'provider_id' => $provider->id,
                'appointment_type_id' => $type->id,
                'chair_id' => $chair->id,
                'scheduled_start' => '2026-09-01 09:00',
                'scheduled_end' => '2026-09-01 09:30',
                'reason' => 'Cleaning',
            ]);

        $response->assertRedirect(route('appointments.index'));

        $appointment = Appointment::first();
        $this->assertNotNull($appointment);
        $this->assertMatchesRegularExpression('/^APT-\d{4}-\d{6}$/', $appointment->appointment_number);
        $this->assertSame('scheduled', $appointment->status);
    }

    public function test_an_appointment_can_be_edited(): void
    {
        $appointment = Appointment::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->put(route('appointments.update', $appointment), [
                'patient_id' => $appointment->patient_id,
                'provider_id' => $appointment->provider_id,
                'appointment_type_id' => $appointment->appointment_type_id,
                'chair_id' => $appointment->chair_id,
                'scheduled_start' => $appointment->scheduled_start->format('Y-m-d H:i'),
                'scheduled_end' => $appointment->scheduled_end->format('Y-m-d H:i'),
                'reason' => 'Updated reason',
            ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertSame('Updated reason', $appointment->fresh()->reason);
    }

    public function test_an_appointment_can_be_rescheduled(): void
    {
        $appointment = Appointment::factory()->create([
            'scheduled_start' => '2026-09-01 09:00:00',
            'scheduled_end' => '2026-09-01 09:30:00',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->patch(route('appointments.reschedule', $appointment), [
                'scheduled_start' => '2026-09-02 10:00',
                'scheduled_end' => '2026-09-02 10:30',
            ]);

        $response->assertRedirect(route('appointments.index'));

        $appointment->refresh();
        $this->assertSame('rescheduled', $appointment->status);
        $this->assertSame('2026-09-02 10:00', $appointment->scheduled_start->format('Y-m-d H:i'));
    }

    public function test_an_appointment_can_be_cancelled(): void
    {
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('appointments.cancel', $appointment));

        $response->assertRedirect(route('appointments.index'));

        $appointment->refresh();
        $this->assertSame('cancelled', $appointment->status);
        $this->assertNotNull($appointment->cancelled_at);
    }

    public function test_an_appointment_can_be_marked_as_no_show(): void
    {
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('appointments.no-show', $appointment));

        $response->assertRedirect(route('appointments.index'));
        $this->assertSame('no_show', $appointment->fresh()->status);
    }

    public function test_cancelling_an_appointment_frees_the_slot_for_a_new_booking(): void
    {
        $provider = Provider::factory()->create();
        $type = AppointmentType::factory()->create();
        $patient = Patient::factory()->create();

        $appointment = Appointment::factory()->create([
            'provider_id' => $provider->id,
            'appointment_type_id' => $type->id,
            'chair_id' => null,
            'scheduled_start' => '2026-09-01 09:00:00',
            'scheduled_end' => '2026-09-01 09:30:00',
            'status' => 'scheduled',
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('appointments.cancel', $appointment))
            ->assertRedirect(route('appointments.index'));

        $response = $this->actingAs(User::factory()->create())
            ->post(route('appointments.store'), [
                'patient_id' => $patient->id,
                'provider_id' => $provider->id,
                'appointment_type_id' => $type->id,
                'scheduled_start' => '2026-09-01 09:00',
                'scheduled_end' => '2026-09-01 09:30',
            ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertSame(2, Appointment::count());
    }
}
