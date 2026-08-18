<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceAdjustment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceAdjustmentController extends Controller
{
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(InvoiceAdjustment::TYPES)],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string'],
        ]);

        $invoice->adjustments()->create([
            'type' => $data['type'],
            'amount' => $data['amount'],
            'reason' => $data['reason'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('status', 'Adjustment recorded.');
    }
}
