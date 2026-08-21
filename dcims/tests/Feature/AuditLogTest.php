<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_the_audit_log(): void
    {
        $this->get(route('audit-logs.index'))->assertRedirect(route('login'));
    }

    public function test_creating_a_patient_records_an_audit_log_entry(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('patients.store'), [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'date_of_birth' => '1990-05-15',
            'sex' => 'male',
            'registration_date' => now()->format('Y-m-d'),
        ]);

        $patient = Patient::firstOrFail();
        $log = AuditLog::where('entity_type', 'patients')->where('entity_id', $patient->id)->firstOrFail();

        $this->assertSame('create', $log->action);
        $this->assertSame($user->id, $log->actor_id);
        $this->assertNull($log->old_values);
        $this->assertSame('Juan', $log->new_values['first_name']);
    }

    public function test_updating_a_patient_records_only_the_changed_fields(): void
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create(['occupation' => 'Teacher']);

        $this->actingAs($user)->put(route('patients.update', $patient), [
            'first_name' => $patient->first_name,
            'last_name' => $patient->last_name,
            'date_of_birth' => $patient->date_of_birth->format('Y-m-d'),
            'sex' => $patient->sex,
            'registration_date' => $patient->registration_date->format('Y-m-d'),
            'occupation' => 'Engineer',
        ]);

        $log = AuditLog::where('entity_type', 'patients')
            ->where('entity_id', $patient->id)
            ->where('action', 'update')
            ->firstOrFail();

        $this->assertSame('Teacher', $log->old_values['occupation']);
        $this->assertSame('Engineer', $log->new_values['occupation']);
        $this->assertArrayNotHasKey('last_name', $log->new_values);
    }

    public function test_deleting_a_model_records_an_audit_log_entry(): void
    {
        $patient = Patient::factory()->create();
        $patientId = $patient->id;

        $patient->delete();

        $log = AuditLog::where('entity_type', 'patients')
            ->where('entity_id', $patientId)
            ->where('action', 'delete')
            ->firstOrFail();

        $this->assertSame('delete', $log->action);
        $this->assertNull($log->new_values);
        $this->assertNotNull($log->old_values);
    }

    public function test_audit_log_index_can_be_filtered_by_entity_type(): void
    {
        Patient::factory()->create();

        AuditLog::factory()->create(['entity_type' => 'invoices', 'action' => 'create']);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('audit-logs.index', ['entity_type' => 'invoices']));

        $response->assertOk();
        $response->assertViewHas('logs', fn ($logs) => $logs->every(fn ($log) => $log->entity_type === 'invoices'));
    }
}
