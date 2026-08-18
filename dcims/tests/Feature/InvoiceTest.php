<?php

namespace Tests\Feature;

use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Procedure;
use App\Models\ProcedureRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_invoices(): void
    {
        $this->get(route('invoices.index'))->assertRedirect(route('login'));
    }

    public function test_generating_an_invoice_from_an_encounter_creates_it_with_line_items(): void
    {
        $encounter = Encounter::factory()->create();
        $procedure = Procedure::factory()->create(['name' => 'Composite Filling']);
        $record = ProcedureRecord::factory()->create([
            'encounter_id' => $encounter->id,
            'patient_id' => $encounter->patient_id,
            'procedure_id' => $procedure->id,
            'status' => 'completed',
            'quantity' => 1,
            'unit_price' => 2500,
            'total_amount' => 2500,
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.invoice.generate', $encounter));

        $invoice = Invoice::first();
        $response->assertRedirect(route('invoices.show', $invoice));

        $this->assertNotNull($invoice);
        $this->assertSame($encounter->patient_id, $invoice->patient_id);
        $this->assertSame($encounter->id, $invoice->encounter_id);
        $this->assertEquals(2500, $invoice->subtotal);
        $this->assertEquals(2500, $invoice->total_amount);
        $this->assertEquals(2500, $invoice->balance, 'A freshly generated invoice with no payments owes its full total.');

        $item = $invoice->items()->first();
        $this->assertSame($record->id, $item->procedure_record_id);
        $this->assertSame('Composite Filling', $item->description);
    }

    public function test_generating_an_invoice_twice_does_not_double_bill_the_same_procedure(): void
    {
        $encounter = Encounter::factory()->create();
        ProcedureRecord::factory()->create([
            'encounter_id' => $encounter->id,
            'patient_id' => $encounter->patient_id,
            'status' => 'completed',
        ]);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('encounters.invoice.generate', $encounter));

        $this->actingAs($user)
            ->post(route('encounters.invoice.generate', $encounter))
            ->assertSessionHasErrors('procedure_records');

        $this->assertSame(1, Invoice::count());
    }

    public function test_balance_and_amount_paid_are_not_mass_assignable(): void
    {
        $invoice = Invoice::factory()->create(['total_amount' => 1000]);

        $invoice->update(['balance' => 1, 'amount_paid' => 999]);

        $this->assertEquals(1000, $invoice->fresh()->balance, 'balance is not in $fillable, so a mass-assignment update() must silently ignore it.');
    }
}
