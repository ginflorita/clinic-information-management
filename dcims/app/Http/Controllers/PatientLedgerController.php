<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PatientLedgerController extends Controller
{
    public function show(Patient $patient): View
    {
        $invoices = $patient->invoices()->where('status', 'issued')->with('items')->get();
        $payments = $patient->payments()->where('status', 'completed')->with('paymentMethod')->get();
        $adjustments = $patient->invoices()->with('adjustments')->get()->flatMap->adjustments;

        $entries = new Collection;

        foreach ($invoices as $invoice) {
            $entries->push([
                'date' => $invoice->invoice_date->toDateString(),
                'description' => $invoice->items->pluck('description')->filter()->implode(', ') ?: $invoice->invoice_number,
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'type' => 0,
            ]);
        }

        foreach ($payments as $payment) {
            $entries->push([
                'date' => $payment->payment_date->toDateString(),
                'description' => 'Payment via '.$payment->paymentMethod->name.($payment->reference_number ? ' (Ref: '.$payment->reference_number.')' : ''),
                'debit' => 0,
                'credit' => $payment->amount,
                'type' => 1,
            ]);
        }

        foreach ($adjustments as $adjustment) {
            $entries->push([
                'date' => $adjustment->created_at->toDateString(),
                'description' => ucfirst(str_replace('_', ' ', $adjustment->type)).($adjustment->reason ? ': '.$adjustment->reason : ''),
                'debit' => 0,
                'credit' => $adjustment->amount,
                'type' => 1,
            ]);
        }

        $entries = $entries->sortBy([['date', 'asc'], ['type', 'asc']])->values();

        $runningBalance = 0;
        $entries = $entries->map(function (array $entry) use (&$runningBalance) {
            $runningBalance += $entry['debit'] - $entry['credit'];
            $entry['balance'] = $runningBalance;

            return $entry;
        });

        return view('patients.ledger', [
            'patient' => $patient,
            'entries' => $entries,
            'runningBalance' => $runningBalance,
        ]);
    }
}
