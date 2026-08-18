<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($invoice, $data, $request) {
            $payment = $invoice->payments()->create([
                'patient_id' => $invoice->patient_id,
                'payment_date' => now()->toDateString(),
                'payment_method_id' => $data['payment_method_id'],
                'amount' => $data['amount'],
                'reference_number' => $data['reference_number'] ?? null,
                'received_by' => $request->user()->id,
                'notes' => $data['notes'] ?? null,
            ]);

            // Every payment, simple or split, is backed by an allocation row —
            // this is the single source of truth the balance trigger sums from.
            $payment->allocations()->create([
                'invoice_id' => $invoice->id,
                'amount_applied' => $data['amount'],
            ]);
        });

        return redirect()->route('invoices.show', $invoice)->with('status', 'Payment recorded.');
    }

    public function storeSplit(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'allocations' => ['required', 'array'],
            'allocations.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $allocations = collect($data['allocations'])
            ->filter(fn ($amount) => $amount !== null && (float) $amount > 0);

        if ($allocations->isEmpty()) {
            throw ValidationException::withMessages([
                'allocations' => 'Enter an amount for at least one invoice.',
            ]);
        }

        $invoices = Invoice::whereIn('id', $allocations->keys())
            ->where('patient_id', $patient->id)
            ->get()
            ->keyBy('id');

        if ($invoices->count() !== $allocations->count()) {
            throw ValidationException::withMessages([
                'allocations' => 'One or more selected invoices do not belong to this patient.',
            ]);
        }

        DB::transaction(function () use ($patient, $data, $allocations, $invoices, $request) {
            $payment = Payment::create([
                'patient_id' => $patient->id,
                'invoice_id' => null,
                'payment_date' => now()->toDateString(),
                'payment_method_id' => $data['payment_method_id'],
                'amount' => $allocations->sum(),
                'reference_number' => $data['reference_number'] ?? null,
                'received_by' => $request->user()->id,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($allocations as $invoiceId => $amount) {
                $payment->allocations()->create([
                    'invoice_id' => $invoices[$invoiceId]->id,
                    'amount_applied' => $amount,
                ]);
            }
        });

        return redirect()->route('patients.show', $patient)->with('status', 'Split payment recorded.');
    }

    public function void(Invoice $invoice, Payment $payment): RedirectResponse
    {
        try {
            $payment->void();
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('invoices.show', $invoice)->with('status', 'Payment voided.');
    }
}
