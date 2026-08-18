<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceAdjustment;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The balance/amount_paid guarantee lives entirely at the DB layer (Postgres
 * trigger, see the invoice_balance_trigger migration) — SQLite has no
 * trigger functions, so none of this is testable there. Run manually against
 * real Postgres to verify (php artisan test --env=testing.pgsql or similar),
 * same precedent as AppointmentDoubleBookingTest's DB-level constraint test.
 */
class InvoiceBalanceTriggerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Requires a real Postgres connection (trigger functions are not supported by SQLite).');
        }
    }

    public function test_recording_a_payment_recalculates_the_balance_via_the_trigger(): void
    {
        $invoice = Invoice::factory()->create(['total_amount' => 2000]);
        $method = PaymentMethod::factory()->create();
        $user = User::factory()->create();

        $payment = Payment::create([
            'patient_id' => $invoice->patient_id,
            'invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'payment_method_id' => $method->id,
            'amount' => 800,
            'status' => 'completed',
            'received_by' => $user->id,
        ]);
        $payment->allocations()->create(['invoice_id' => $invoice->id, 'amount_applied' => 800]);

        $this->assertEquals(1200, $invoice->fresh()->balance);
        $this->assertEquals(800, $invoice->fresh()->amount_paid);
    }

    public function test_recording_an_adjustment_recalculates_the_balance_via_the_trigger(): void
    {
        $invoice = Invoice::factory()->create(['total_amount' => 2000]);
        $user = User::factory()->create();

        InvoiceAdjustment::create([
            'invoice_id' => $invoice->id,
            'type' => 'discount',
            'amount' => 300,
            'created_by' => $user->id,
        ]);

        $this->assertEquals(1700, $invoice->fresh()->balance);
    }

    public function test_voiding_a_payment_recalculates_the_balance_back_up(): void
    {
        $invoice = Invoice::factory()->create(['total_amount' => 2000]);
        $method = PaymentMethod::factory()->create();
        $user = User::factory()->create();

        $payment = Payment::create([
            'patient_id' => $invoice->patient_id,
            'invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'payment_method_id' => $method->id,
            'amount' => 800,
            'status' => 'completed',
            'received_by' => $user->id,
        ]);
        $payment->allocations()->create(['invoice_id' => $invoice->id, 'amount_applied' => 800]);

        $this->assertEquals(1200, $invoice->fresh()->balance);

        $payment->void();

        $this->assertEquals(2000, $invoice->fresh()->balance, 'Voiding a payment excludes it from the trigger\'s SUM, restoring the balance.');
        $this->assertEquals(0, $invoice->fresh()->amount_paid);
    }

    public function test_a_direct_update_to_balance_is_overwritten_by_the_trigger(): void
    {
        $invoice = Invoice::factory()->create(['total_amount' => 2000]);

        DB::table('invoices')->where('id', $invoice->id)->update(['balance' => 999999]);

        $this->assertEquals(2000, $invoice->fresh()->balance, 'The BEFORE UPDATE trigger recomputes balance from actual payments/adjustments regardless of what was written.');
    }

    public function test_a_single_payment_split_across_two_invoices_recalculates_both_balances(): void
    {
        $invoiceA = Invoice::factory()->create(['total_amount' => 1000]);
        $invoiceB = Invoice::factory()->create(['total_amount' => 2000]);
        $method = PaymentMethod::factory()->create();
        $user = User::factory()->create();

        $payment = Payment::create([
            'patient_id' => $invoiceA->patient_id,
            'invoice_id' => null,
            'payment_date' => now()->toDateString(),
            'payment_method_id' => $method->id,
            'amount' => 1500,
            'status' => 'completed',
            'received_by' => $user->id,
        ]);
        $payment->allocations()->create(['invoice_id' => $invoiceA->id, 'amount_applied' => 1000]);
        $payment->allocations()->create(['invoice_id' => $invoiceB->id, 'amount_applied' => 500]);

        $this->assertEquals(0, $invoiceA->fresh()->balance);
        $this->assertEquals(1500, $invoiceB->fresh()->balance);

        $payment->void();

        $this->assertEquals(1000, $invoiceA->fresh()->balance, 'Voiding a split payment restores every invoice it touched.');
        $this->assertEquals(2000, $invoiceB->fresh()->balance);
    }
}
