<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Diagnosis;
use App\Models\Encounter;
use App\Models\EncounterDiagnosis;
use App\Models\OdontogramEntrySurface;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Provider;
use App\Models\Tooth;
use App\Models\ToothCondition;
use App\Models\TreatmentPlanItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EncounterController extends Controller
{
    public function index(): View
    {
        return view('encounters.index', [
            'encounters' => Encounter::with(['patient', 'provider'])
                ->orderByDesc('started_at')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('encounters.create', [
            'patients' => Patient::where('status', 'active')->orderBy('last_name')->get(),
            'providers' => Provider::where('is_active', true)->orderBy('last_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'provider_id' => ['required', 'exists:providers,id'],
            'chief_complaint' => ['nullable', 'string'],
        ]);
        $data['started_at'] = now();
        $data['status'] = 'in_progress';

        $encounter = Encounter::create($data);

        return redirect()->route('encounters.show', $encounter)->with('status', 'Encounter started.');
    }

    public function startFromAppointment(Appointment $appointment): RedirectResponse
    {
        if ($appointment->encounter) {
            return redirect()->route('encounters.show', $appointment->encounter);
        }

        $encounter = Encounter::create([
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'provider_id' => $appointment->provider_id,
            'status' => 'in_progress',
            'started_at' => now(),
            'chief_complaint' => $appointment->reason,
        ]);

        return redirect()->route('encounters.show', $encounter)->with('status', 'Encounter started.');
    }

    public function show(Encounter $encounter): View
    {
        $encounter->load(['patient', 'provider', 'appointment', 'clinicalNotes' => function ($query) {
            $query->with(['creator', 'signer'])->orderByDesc('created_at');
        }, 'odontogram.entries' => function ($query) {
            $query->with(['tooth', 'condition', 'surfaces'])->orderByDesc('created_at');
        }, 'diagnoses' => function ($query) {
            $query->with(['diagnosis', 'tooth'])->orderByDesc('diagnosed_at');
        }, 'procedureRecords' => function ($query) {
            $query->with(['procedure', 'tooth', 'treatmentPlanItem'])->orderByDesc('performed_at');
        }]);

        $outstandingPlanItems = TreatmentPlanItem::whereHas('treatmentPlan', function ($query) use ($encounter) {
            $query->where('patient_id', $encounter->patient_id);
        })->where('status', 'accepted')->with(['procedure', 'tooth', 'treatmentPlan'])->get();

        return view('encounters.show', [
            'encounter' => $encounter,
            'teeth' => Tooth::where('is_active', true)->where('dentition_type', 'permanent')->orderBy('arch')->orderBy('position')->get(),
            'toothConditions' => ToothCondition::where('is_active', true)->orderBy('name')->get(),
            'surfaces' => OdontogramEntrySurface::SURFACES,
            'diagnosisOptions' => Diagnosis::where('is_active', true)->orderBy('name')->get(),
            'diagnosisStatuses' => EncounterDiagnosis::STATUSES,
            'procedures' => Procedure::where('is_active', true)->orderBy('name')->get(),
            'outstandingPlanItems' => $outstandingPlanItems,
        ]);
    }

    public function complete(Encounter $encounter): RedirectResponse
    {
        $encounter->complete();

        return redirect()->route('encounters.show', $encounter)->with('status', 'Encounter completed.');
    }
}
