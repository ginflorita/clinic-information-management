<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\AppointmentType;
use App\Models\Chair;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_view_the_booking_form(): void
    {
        $this->get(route('book-appointment.create'))->assertOk();
    }

    public function test_guest_can_submit_a_request_for_an_existing_patient(): void
    {
        $patient = Patient::factory()->create();
        $type = AppointmentType::factory()->create();

        $response = $this->post(route('book-appointment.store'), [
            'patient_number' => $patient->patient_number,
            'date_of_birth' => $patient->date_of_birth->toDateString(),
            'appointment_type_id' => $type->id,
            'preferred_date' => now()->addDays(3)->toDateString(),
            'preferred_time_period' => 'morning',
            'reason' => 'Toothache',
            'contact_phone' => '555-1234',
        ]);

        $response->assertRedirect(route('book-appointment.create'));
        $this->assertDatabaseHas('appointment_requests', [
            'patient_id' => $patient->id,
            'appointment_type_id' => $type->id,
            'status' => 'pending',
            'reason' => 'Toothache',
        ]);
        $this->assertNotNull(AppointmentRequest::first()->reference_number);
    }

    public function test_booking_fails_when_patient_details_do_not_match(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->post(route('book-appointment.store'), [
            'patient_number' => $patient->patient_number,
            'date_of_birth' => now()->subYears(40)->toDateString(),
            'preferred_date' => now()->addDays(3)->toDateString(),
            'preferred_time_period' => 'morning',
            'contact_phone' => '555-1234',
        ]);

        $response->assertSessionHasErrors('patient_number');
        $this->assertDatabaseCount('appointment_requests', 0);
    }

    public function test_booking_requires_at_least_one_contact_method(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->post(route('book-appointment.store'), [
            'patient_number' => $patient->patient_number,
            'date_of_birth' => $patient->date_of_birth->toDateString(),
            'preferred_date' => now()->addDays(3)->toDateString(),
            'preferred_time_period' => 'morning',
        ]);

        $response->assertSessionHasErrors(['contact_phone', 'contact_email']);
        $this->assertDatabaseCount('appointment_requests', 0);
    }

    public function test_guest_cannot_view_the_staff_index(): void
    {
        $this->get(route('appointment-requests.index'))->assertRedirect(route('login'));
    }

    public function test_staff_can_list_and_filter_requests_by_status(): void
    {
        AppointmentRequest::factory()->create(['status' => 'pending']);
        AppointmentRequest::factory()->create(['status' => 'declined']);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('appointment-requests.index', ['status' => 'pending']));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('appointmentRequests'));
    }

    public function test_staff_can_decline_a_pending_request(): void
    {
        $appointmentRequest = AppointmentRequest::factory()->create(['status' => 'pending']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('appointment-requests.decline', $appointmentRequest), ['staff_notes' => 'No slots available']);

        $response->assertRedirect(route('appointment-requests.index'));
        $appointmentRequest->refresh();
        $this->assertSame('declined', $appointmentRequest->status);
        $this->assertSame('No slots available', $appointmentRequest->staff_notes);
        $this->assertSame($user->id, $appointmentRequest->reviewed_by);
        $this->assertNotNull($appointmentRequest->reviewed_at);
    }

    public function test_declining_an_already_reviewed_request_fails(): void
    {
        $appointmentRequest = AppointmentRequest::factory()->create(['status' => 'declined']);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('appointment-requests.decline', $appointmentRequest));

        $response->assertSessionHasErrors('status');
    }

    public function test_confirming_via_the_appointment_form_links_and_updates_the_request(): void
    {
        $appointmentRequest = AppointmentRequest::factory()->create(['status' => 'pending']);
        $provider = Provider::factory()->create();
        $chair = Chair::factory()->create();
        $type = AppointmentType::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('appointments.store'), [
            'appointment_request_id' => $appointmentRequest->id,
            'patient_id' => $appointmentRequest->patient_id,
            'provider_id' => $provider->id,
            'chair_id' => $chair->id,
            'appointment_type_id' => $type->id,
            'scheduled_start' => now()->addDays(3)->setTime(9, 0)->format('Y-m-d\TH:i'),
            'scheduled_end' => now()->addDays(3)->setTime(9, 30)->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('appointments.index'));
        $appointmentRequest->refresh();
        $this->assertSame('confirmed', $appointmentRequest->status);
        $this->assertSame($user->id, $appointmentRequest->reviewed_by);
        $this->assertNotNull($appointmentRequest->appointment_id);
        $this->assertSame(1, Appointment::count());
    }

    public function test_the_create_appointment_form_prefills_from_a_pending_request(): void
    {
        $appointmentRequest = AppointmentRequest::factory()->create(['status' => 'pending']);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('appointments.create', ['appointment_request_id' => $appointmentRequest->id]));

        $response->assertOk();
        $this->assertTrue($response->viewData('appointmentRequest')->is($appointmentRequest));
    }
}
