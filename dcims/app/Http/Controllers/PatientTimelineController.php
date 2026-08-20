<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PatientTimelineController extends Controller
{
    public function show(Patient $patient): View
    {
        $entries = new Collection;

        $entries->push([
            'datetime' => $patient->created_at,
            'description' => 'Patient registered',
        ]);

        foreach ($patient->encounters as $encounter) {
            $entries->push([
                'datetime' => $encounter->started_at,
                'description' => 'Encounter: '.($encounter->chief_complaint ?: 'Visit'),
            ]);
        }

        foreach ($patient->diagnoses()->with('diagnosis')->get() as $diagnosis) {
            $entries->push([
                'datetime' => $diagnosis->diagnosed_at,
                'description' => 'Diagnosis added: '.$diagnosis->diagnosis->name,
            ]);
        }

        foreach ($patient->treatmentPlans as $plan) {
            $entries->push([
                'datetime' => $plan->created_at,
                'description' => 'Treatment plan created: '.$plan->title,
            ]);

            if ($plan->accepted_at) {
                $entries->push([
                    'datetime' => $plan->accepted_at,
                    'description' => 'Treatment plan accepted: '.$plan->title,
                ]);
            }
        }

        foreach ($patient->procedureRecords()->where('status', 'completed')->with('procedure')->get() as $record) {
            $entries->push([
                'datetime' => $record->performed_at,
                'description' => 'Procedure performed: '.$record->procedure->name,
            ]);
        }

        foreach ($patient->payments()->where('status', 'completed')->get() as $payment) {
            $entries->push([
                'datetime' => $payment->payment_date,
                'description' => 'Payment received: '.number_format($payment->amount, 2),
            ]);
        }

        $entries = $entries->sortBy('datetime')->values();

        return view('patients.timeline', [
            'patient' => $patient,
            'entries' => $entries,
        ]);
    }
}
