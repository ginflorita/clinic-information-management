<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\View\View;

class OdontogramController extends Controller
{
    public function show(Patient $patient): View
    {
        $odontograms = $patient->odontograms()
            ->with(['entries.tooth', 'entries.condition', 'entries.surfaces', 'recorder'])
            ->orderBy('recorded_at')
            ->get();

        $entriesByTooth = $odontograms
            ->flatMap(fn ($odontogram) => $odontogram->entries->each(
                fn ($entry) => $entry->setRelation('odontogram', $odontogram)
            ))
            ->groupBy(fn ($entry) => $entry->tooth->tooth_code)
            ->sortKeys();

        return view('patients.odontogram', ['patient' => $patient, 'entriesByTooth' => $entriesByTooth]);
    }
}
