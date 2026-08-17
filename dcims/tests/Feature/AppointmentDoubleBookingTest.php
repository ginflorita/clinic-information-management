<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Chair;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AppointmentDoubleBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_overlapping_provider_booking_is_rejected_at_the_application_layer(): void
    {
        $provider = Provider::factory()->create();
        $type = AppointmentType::factory()->create();

        $existing = Appointment::factory()->create([
            'provider_id' => $provider->id,
            'chair_id' => null,
            'scheduled_start' => '2026-09-01 09:00:00',
            'scheduled_end' => '2026-09-01 09:30:00',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('appointments.store'), [
                'patient_id' => Patient::factory()->create()->id,
                'provider_id' => $provider->id,
                'appointment_type_id' => $type->id,
                'scheduled_start' => '2026-09-01 09:15',
                'scheduled_end' => '2026-09-01 09:45',
            ]);

        $response->assertSessionHasErrors('scheduled_start');
        $this->assertSame(1, Appointment::count());
    }

    public function test_overlapping_chair_booking_is_rejected_even_with_different_providers(): void
    {
        $chair = Chair::factory()->create();
        $type = AppointmentType::factory()->create();

        Appointment::factory()->create([
            'chair_id' => $chair->id,
            'scheduled_start' => '2026-09-01 09:00:00',
            'scheduled_end' => '2026-09-01 09:30:00',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('appointments.store'), [
                'patient_id' => Patient::factory()->create()->id,
                'provider_id' => Provider::factory()->create()->id,
                'appointment_type_id' => $type->id,
                'chair_id' => $chair->id,
                'scheduled_start' => '2026-09-01 09:15',
                'scheduled_end' => '2026-09-01 09:45',
            ]);

        $response->assertSessionHasErrors('scheduled_start');
    }

    public function test_non_overlapping_bookings_for_the_same_provider_are_allowed(): void
    {
        $provider = Provider::factory()->create();
        $type = AppointmentType::factory()->create();

        Appointment::factory()->create([
            'provider_id' => $provider->id,
            'chair_id' => null,
            'scheduled_start' => '2026-09-01 09:00:00',
            'scheduled_end' => '2026-09-01 09:30:00',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('appointments.store'), [
                'patient_id' => Patient::factory()->create()->id,
                'provider_id' => $provider->id,
                'appointment_type_id' => $type->id,
                'scheduled_start' => '2026-09-01 09:30',
                'scheduled_end' => '2026-09-01 10:00',
            ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertSame(2, Appointment::count());
    }

    public function test_cancelled_appointments_do_not_count_as_conflicts(): void
    {
        $provider = Provider::factory()->create();
        $type = AppointmentType::factory()->create();

        Appointment::factory()->create([
            'provider_id' => $provider->id,
            'chair_id' => null,
            'scheduled_start' => '2026-09-01 09:00:00',
            'scheduled_end' => '2026-09-01 09:30:00',
            'status' => 'cancelled',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('appointments.store'), [
                'patient_id' => Patient::factory()->create()->id,
                'provider_id' => $provider->id,
                'appointment_type_id' => $type->id,
                'scheduled_start' => '2026-09-01 09:00',
                'scheduled_end' => '2026-09-01 09:30',
            ]);

        $response->assertRedirect(route('appointments.index'));
    }

    /**
     * The real guarantee lives at the DB layer (Postgres EXCLUDE USING gist,
     * see the appointments migration) — SQLite has no gist index type and no
     * btree_gist extension, so this can only run against Postgres. This proves
     * a direct DB insert that bypasses the app entirely is still rejected,
     * which is the specific requirement the build playbook calls out.
     */
    public function test_db_level_constraint_rejects_overlapping_insert_bypassing_the_app(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Requires a real Postgres connection (EXCLUDE USING gist is not supported by SQLite).');
        }

        $provider = Provider::factory()->create();
        $type = AppointmentType::factory()->create();
        $patient = Patient::factory()->create();

        DB::table('appointments')->insert([
            'appointment_number' => 'APT-TEST-1',
            'patient_id' => $patient->id,
            'provider_id' => $provider->id,
            'appointment_type_id' => $type->id,
            'scheduled_start' => '2026-09-01 09:00:00',
            'scheduled_end' => '2026-09-01 09:30:00',
            'status' => 'scheduled',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('appointments')->insert([
            'appointment_number' => 'APT-TEST-2',
            'patient_id' => $patient->id,
            'provider_id' => $provider->id,
            'appointment_type_id' => $type->id,
            'scheduled_start' => '2026-09-01 09:15:00',
            'scheduled_end' => '2026-09-01 09:45:00',
            'status' => 'scheduled',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
