<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\View\View;

class PerioController extends Controller
{
    public function show(Patient $patient): View
    {
        $examinations = $patient->perioExaminations()
            ->with(['toothRecords' => function ($query) {
                $query->with(['tooth', 'measurements'])->orderBy('tooth_id');
            }, 'examiner'])
            ->orderBy('examined_at')
            ->get();

        return view('patients.periodontal', ['patient' => $patient, 'examinations' => $examinations]);
    }
}
