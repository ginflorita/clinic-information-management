<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Provider;
use App\Models\Tooth;
use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use LogicException;

class TreatmentPlanController extends Controller
{
    public function index(): View
    {
        return view('treatment-plans.index', [
            'treatmentPlans' => TreatmentPlan::with(['patient', 'provider'])
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('treatment-plans.create', [
            'patients' => Patient::where('status', 'active')->orderBy('last_name')->get(),
            'providers' => Provider::where('is_active', true)->orderBy('last_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'provider_id' => ['required', 'exists:providers,id'],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $plan = TreatmentPlan::create($data);

        return redirect()->route('treatment-plans.show', $plan)->with('status', 'Treatment plan created.');
    }

    public function show(TreatmentPlan $treatmentPlan): View
    {
        $treatmentPlan->load(['patient', 'provider', 'items.procedure', 'items.tooth']);

        return view('treatment-plans.show', [
            'treatmentPlan' => $treatmentPlan,
            'procedures' => Procedure::where('is_active', true)->orderBy('name')->get(),
            'teeth' => Tooth::where('is_active', true)->where('dentition_type', 'permanent')->orderBy('arch')->orderBy('position')->get(),
            'itemStatuses' => TreatmentPlanItem::STATUSES,
            'availableTransitions' => TreatmentPlan::TRANSITIONS[$treatmentPlan->status] ?? [],
        ]);
    }

    public function transition(Request $request, TreatmentPlan $treatmentPlan): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(TreatmentPlan::STATUSES)],
        ]);

        try {
            $treatmentPlan->transitionTo($data['status']);
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('treatment-plans.show', $treatmentPlan)->with('status', 'Treatment plan updated.');
    }
}
