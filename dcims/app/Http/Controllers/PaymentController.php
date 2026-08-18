<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $invoice->payments()->create([
            'patient_id' => $invoice->patient_id,
            'payment_date' => now()->toDateString(),
            'payment_method_id' => $data['payment_method_id'],
            'amount' => $data['amount'],
            'reference_number' => $data['reference_number'] ?? null,
            'received_by' => $request->user()->id,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('status', 'Payment recorded.');
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
