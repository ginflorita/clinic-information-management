<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\InvoiceAdjustment;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        return view('invoices.index', [
            'invoices' => Invoice::with('patient')->orderByDesc('created_at')->get(),
        ]);
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['patient', 'encounter', 'items.procedure', 'allocations.payment.paymentMethod', 'adjustments.creator']);

        return view('invoices.show', [
            'invoice' => $invoice,
            'paymentMethods' => PaymentMethod::where('is_active', true)->orderBy('name')->get(),
            'adjustmentTypes' => InvoiceAdjustment::TYPES,
        ]);
    }

    public function generateFromEncounter(Encounter $encounter): RedirectResponse
    {
        $records = $encounter->procedureRecords()
            ->where('status', 'completed')
            ->whereDoesntHave('invoiceItem')
            ->with('procedure')
            ->get();

        if ($records->isEmpty()) {
            throw ValidationException::withMessages([
                'procedure_records' => 'No completed, un-invoiced procedures are available on this encounter.',
            ]);
        }

        $invoice = DB::transaction(function () use ($encounter, $records) {
            $subtotal = $records->sum('total_amount');

            $invoice = Invoice::create([
                'patient_id' => $encounter->patient_id,
                'encounter_id' => $encounter->id,
                'invoice_date' => now()->toDateString(),
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
            ]);

            foreach ($records as $record) {
                $invoice->items()->create([
                    'procedure_id' => $record->procedure_id,
                    'treatment_plan_item_id' => $record->treatment_plan_item_id,
                    'procedure_record_id' => $record->id,
                    'description' => $record->procedure->name,
                    'quantity' => $record->quantity,
                    'unit_price' => $record->unit_price,
                    'total_amount' => $record->total_amount,
                ]);
            }

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice generated.');
    }
}
