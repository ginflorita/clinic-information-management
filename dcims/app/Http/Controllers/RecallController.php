<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Recall;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use LogicException;

class RecallController extends Controller
{
    public function index(): View
    {
        return view('recalls.index', [
            'recalls' => Recall::with(['patient', 'recallType'])->orderBy('due_date')->get(),
        ]);
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'recall_type_id' => ['required', 'exists:recall_types,id'],
            'due_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
        $data['created_by'] = $request->user()->id;

        $patient->recalls()->create($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Recall scheduled.');
    }

    public function complete(Recall $recall): RedirectResponse
    {
        try {
            $recall->complete();
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('recalls.index')->with('status', 'Recall marked completed.');
    }

    public function cancel(Recall $recall): RedirectResponse
    {
        try {
            $recall->cancel();
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('recalls.index')->with('status', 'Recall cancelled.');
    }
}
