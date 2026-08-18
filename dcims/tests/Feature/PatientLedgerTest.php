<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceAdjustment;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PatientLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_the_ledger(): void
    {
        $patient = Patient::factory()->create();

        $this->get(route('patients.ledger.show', $patient))->assertRedirect(route('login'));
    }

    public function test_an_issued_invoice_appears_as_a_debit(): void
    {
        $patient = Patient::factory()->create();
        Invoice::factory()->create(['patient_id' => $patient->id, 'status' => 'issued', 'total_amount' => 800]);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.ledger.show', $patient));

        $response->assertOk();
        $response->assertSee('800.00');
    }

    public function test_a_cancelled_invoice_does_not_appear(): void
    {
        $patient = Patient::factory()->create();
        Invoice::factory()->create(['patient_id' => $patient->id, 'status' => 'cancelled', 'total_amount' => 800]);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.ledger.show', $patient));

        $response->assertOk();
        $response->assertSee('No billing activity on file.');
    }

    public function test_a_voided_payment_does_not_appear_as_a_credit(): void
    {
        $patient = Patient::factory()->create();
        Payment::factory()->create(['patient_id' => $patient->id, 'status' => 'voided', 'amount' => 500]);

        $response = $this->actingAs(User::factory()->create())->get(route('patients.ledger.show', $patient));

        $response->assertOk();
        $response->assertSee('No billing activity on file.');
    }

    public function test_the_running_balance_matches_the_sum_of_invoices_payments_and_adjustments(): void
    {
        $patient = Patient::factory()->create();
        $user = User::factory()->create();

        Invoice::factory()->create(['patient_id' => $patient->id, 'status' => 'issued', 'total_amount' => 800]);
        Invoice::factory()->create(['patient_id' => $patient->id, 'status' => 'issued', 'total_amount' => 1200]);
        $paidInvoice = Invoice::factory()->create(['patient_id' => $patient->id, 'status' => 'issued', 'total_amount' => 500]);
        Payment::factory()->create(['patient_id' => $patient->id, 'status' => 'completed', 'amount' => 500]);
        InvoiceAdjustment::factory()->create(['invoice_id' => $paidInvoice->id, 'amount' => 100, 'created_by' => $user->id]);

        $expectedBalance = 800 + 1200 + 500 - 500 - 100;

        $response = $this->actingAs($user)->get(route('patients.ledger.show', $patient));

        $response->assertOk();
        $response->assertSee(number_format($expectedBalance, 2));
    }

    /**
     * The ledger's running balance is computed independently in PHP from raw
     * transaction amounts (see the test above). This cross-checks that
     * independent computation against the DB-trigger-derived balance already
     * proven correct in InvoiceBalanceTriggerTest — Postgres-only since the
     * trigger doesn't run under SQLite.
     */
    public function test_the_running_balance_matches_the_sum_of_each_invoices_trigger_derived_balance(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Requires a real Postgres connection (trigger functions are not supported by SQLite).');
        }

        $patient = Patient::factory()->create();
        $invoiceA = Invoice::factory()->create(['patient_id' => $patient->id, 'status' => 'issued', 'total_amount' => 1000]);
        $invoiceB = Invoice::factory()->create(['patient_id' => $patient->id, 'status' => 'issued', 'total_amount' => 2000]);

        $payment = Payment::create([
            'patient_id' => $patient->id,
            'invoice_id' => $invoiceA->id,
            'payment_date' => now()->toDateString(),
            'payment_method_id' => PaymentMethod::factory()->create()->id,
            'amount' => 400,
            'status' => 'completed',
            'received_by' => User::factory()->create()->id,
        ]);
        $payment->allocations()->create(['invoice_id' => $invoiceA->id, 'amount_applied' => 400]);

        $expectedFromTrigger = $invoiceA->fresh()->balance + $invoiceB->fresh()->balance;

        $response = $this->actingAs(User::factory()->create())->get(route('patients.ledger.show', $patient));

        $response->assertOk();
        $response->assertSee(number_format($expectedFromTrigger, 2));
    }
}
