<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\ProcedureRecord;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_reports(): void
    {
        $this->get(route('reports.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_each_report(): void
    {
        $user = User::factory()->create();

        foreach (['reports.index', 'reports.patients', 'reports.appointments', 'reports.clinical', 'reports.financial', 'reports.inventory'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }

    public function test_patient_report_counts_by_status(): void
    {
        Patient::factory()->count(2)->create(['status' => 'active']);
        Patient::factory()->create(['status' => 'archived']);

        $response = $this->actingAs(User::factory()->create())->get(route('reports.patients'));

        $response->assertOk();
        $this->assertSame(3, $response->viewData('totalPatients'));
        $this->assertSame(2, $response->viewData('statusCounts')['active']);
        $this->assertSame(1, $response->viewData('statusCounts')['archived']);
    }

    public function test_appointment_report_filters_by_date_range_and_counts_status(): void
    {
        Appointment::factory()->create(['scheduled_start' => now(), 'status' => 'scheduled']);
        Appointment::factory()->create(['scheduled_start' => now(), 'status' => 'cancelled']);
        Appointment::factory()->create(['scheduled_start' => now()->subYears(2), 'status' => 'scheduled']);

        $response = $this->actingAs(User::factory()->create())->get(route('reports.appointments', [
            'from' => now()->startOfMonth()->toDateString(),
            'to' => now()->toDateString(),
        ]));

        $response->assertOk();
        $this->assertSame(2, $response->viewData('total'));
        $this->assertSame(1, $response->viewData('statusCounts')['scheduled']);
        $this->assertSame(1, $response->viewData('statusCounts')['cancelled']);
    }

    public function test_financial_report_computes_revenue_by_provider(): void
    {
        $providerA = Provider::factory()->create();
        $providerB = Provider::factory()->create();
        $encounter = Encounter::factory()->create();

        ProcedureRecord::factory()->create([
            'provider_id' => $providerA->id,
            'encounter_id' => $encounter->id,
            'status' => 'completed',
            'total_amount' => 1000,
            'performed_at' => now(),
        ]);
        ProcedureRecord::factory()->create([
            'provider_id' => $providerB->id,
            'encounter_id' => $encounter->id,
            'status' => 'completed',
            'total_amount' => 500,
            'performed_at' => now(),
        ]);
        ProcedureRecord::factory()->create([
            'provider_id' => $providerA->id,
            'encounter_id' => $encounter->id,
            'status' => 'voided',
            'total_amount' => 9999,
            'performed_at' => now(),
        ]);

        $response = $this->actingAs(User::factory()->create())->get(route('reports.financial', [
            'from' => now()->startOfMonth()->toDateString(),
            'to' => now()->toDateString(),
        ]));

        $response->assertOk();
        $revenueByProvider = $response->viewData('revenueByProvider')->keyBy('provider_id');
        $this->assertEquals(1000, $revenueByProvider[$providerA->id]->revenue);
        $this->assertEquals(500, $revenueByProvider[$providerB->id]->revenue);
    }

    public function test_inventory_report_lists_low_stock_products(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('reports.inventory'));

        $response->assertOk();
        $response->assertViewHas('lowStockProducts');
        $response->assertViewHas('expiringBatches');
    }
}
