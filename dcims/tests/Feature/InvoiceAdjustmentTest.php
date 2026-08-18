<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceAdjustment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_record_an_adjustment(): void
    {
        $invoice = Invoice::factory()->create();

        $this->post(route('invoices.adjustments.store', $invoice))->assertRedirect(route('login'));
    }

    public function test_an_adjustment_can_be_recorded_against_an_invoice(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('invoices.adjustments.store', $invoice), [
                'type' => 'write_off',
                'amount' => 250,
                'reason' => 'Goodwill gesture',
            ]);

        $response->assertRedirect(route('invoices.show', $invoice));

        $adjustment = InvoiceAdjustment::first();
        $this->assertSame($invoice->id, $adjustment->invoice_id);
        $this->assertSame('write_off', $adjustment->type);
        $this->assertEquals(250, $adjustment->amount);
        $this->assertSame('Goodwill gesture', $adjustment->reason);
    }

    public function test_an_invalid_adjustment_type_is_rejected(): void
    {
        $invoice = Invoice::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('invoices.adjustments.store', $invoice), [
                'type' => 'bogus',
                'amount' => 100,
            ])
            ->assertSessionHasErrors('type');
    }
}
