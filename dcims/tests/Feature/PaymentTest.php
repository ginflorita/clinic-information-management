<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_record_a_payment(): void
    {
        $invoice = Invoice::factory()->create();

        $this->post(route('invoices.payments.store', $invoice))->assertRedirect(route('login'));
    }

    public function test_a_payment_can_be_recorded_against_an_invoice(): void
    {
        $invoice = Invoice::factory()->create();
        $method = PaymentMethod::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('invoices.payments.store', $invoice), [
                'payment_method_id' => $method->id,
                'amount' => 500,
                'reference_number' => 'REF-123',
            ]);

        $response->assertRedirect(route('invoices.show', $invoice));

        $payment = Payment::first();
        $this->assertSame($invoice->id, $payment->invoice_id);
        $this->assertSame($invoice->patient_id, $payment->patient_id);
        $this->assertEquals(500, $payment->amount);
        $this->assertSame('REF-123', $payment->reference_number);
        $this->assertSame('completed', $payment->status);
        $this->assertMatchesRegularExpression('/^PAY-\d{4}-\d{6}$/', $payment->payment_number);
    }

    public function test_the_simple_case_also_creates_a_matching_allocation_row(): void
    {
        $invoice = Invoice::factory()->create();
        $method = PaymentMethod::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('invoices.payments.store', $invoice), [
                'payment_method_id' => $method->id,
                'amount' => 500,
            ]);

        $payment = Payment::first();
        $this->assertSame(1, $payment->allocations()->count());
        $allocation = $payment->allocations()->first();
        $this->assertSame($invoice->id, $allocation->invoice_id);
        $this->assertEquals(500, $allocation->amount_applied);
        $this->assertEquals($payment->amount, $payment->allocations()->sum('amount_applied'));
    }

    public function test_a_completed_payment_can_be_voided(): void
    {
        $invoice = Invoice::factory()->create();
        $payment = Payment::factory()->create(['invoice_id' => $invoice->id, 'status' => 'completed']);

        $this->actingAs(User::factory()->create())
            ->post(route('invoices.payments.void', [$invoice, $payment]));

        $this->assertSame('voided', $payment->fresh()->status);
    }

    public function test_an_already_voided_payment_cannot_be_voided_again(): void
    {
        $invoice = Invoice::factory()->create();
        $payment = Payment::factory()->create(['invoice_id' => $invoice->id, 'status' => 'voided']);

        $this->actingAs(User::factory()->create())
            ->post(route('invoices.payments.void', [$invoice, $payment]))
            ->assertSessionHasErrors('status');
    }
}
