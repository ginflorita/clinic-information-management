<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SplitPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_record_a_split_payment(): void
    {
        $patient = Patient::factory()->create();

        $this->post(route('patients.payments.store', $patient))->assertRedirect(route('login'));
    }

    public function test_a_single_payment_can_be_split_across_two_invoices(): void
    {
        $patient = Patient::factory()->create();
        $invoiceA = Invoice::factory()->create(['patient_id' => $patient->id, 'total_amount' => 1000]);
        $invoiceB = Invoice::factory()->create(['patient_id' => $patient->id, 'total_amount' => 2000]);
        $method = PaymentMethod::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('patients.payments.store', $patient), [
                'payment_method_id' => $method->id,
                'reference_number' => 'SPLIT-1',
                'allocations' => [
                    $invoiceA->id => 1000,
                    $invoiceB->id => 500,
                ],
            ]);

        $response->assertRedirect(route('patients.show', $patient));

        $payment = Payment::first();
        $this->assertNotNull($payment);
        $this->assertNull($payment->invoice_id, 'A split payment does not belong to a single invoice.');
        $this->assertEquals(1500, $payment->amount);
        $this->assertSame($patient->id, $payment->patient_id);

        $this->assertSame(2, $payment->allocations()->count());
        $this->assertEquals(
            $payment->amount,
            $payment->allocations()->sum('amount_applied'),
            'SUM(payment_allocations.amount_applied) must equal payments.amount.'
        );

        $this->assertDatabaseHas('payment_allocations', ['payment_id' => $payment->id, 'invoice_id' => $invoiceA->id, 'amount_applied' => 1000]);
        $this->assertDatabaseHas('payment_allocations', ['payment_id' => $payment->id, 'invoice_id' => $invoiceB->id, 'amount_applied' => 500]);
    }

    public function test_allocations_with_zero_or_blank_amounts_are_ignored(): void
    {
        $patient = Patient::factory()->create();
        $invoiceA = Invoice::factory()->create(['patient_id' => $patient->id]);
        $invoiceB = Invoice::factory()->create(['patient_id' => $patient->id]);
        $method = PaymentMethod::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.payments.store', $patient), [
                'payment_method_id' => $method->id,
                'allocations' => [
                    $invoiceA->id => 300,
                    $invoiceB->id => 0,
                ],
            ]);

        $payment = Payment::first();
        $this->assertEquals(300, $payment->amount);
        $this->assertSame(1, $payment->allocations()->count());
    }

    public function test_an_empty_allocation_set_is_rejected(): void
    {
        $patient = Patient::factory()->create();
        $method = PaymentMethod::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.payments.store', $patient), [
                'payment_method_id' => $method->id,
                'allocations' => [],
            ])
            ->assertSessionHasErrors('allocations');
    }

    public function test_an_invoice_belonging_to_another_patient_is_rejected(): void
    {
        $patient = Patient::factory()->create();
        $otherPatientInvoice = Invoice::factory()->create();
        $method = PaymentMethod::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.payments.store', $patient), [
                'payment_method_id' => $method->id,
                'allocations' => [$otherPatientInvoice->id => 100],
            ])
            ->assertSessionHasErrors('allocations');

        $this->assertSame(0, Payment::count());
    }
}
