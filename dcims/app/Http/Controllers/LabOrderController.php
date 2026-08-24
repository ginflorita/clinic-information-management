<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Tooth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use LogicException;

class LabOrderController extends Controller
{
    public function index(): View
    {
        return view('lab-orders.index', [
            'labOrders' => LabOrder::with(['patient', 'lab', 'procedure'])->orderByDesc('created_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('lab-orders.create', [
            'patients' => Patient::where('status', 'active')->orderBy('last_name')->get(),
            'labs' => Lab::where('is_active', true)->orderBy('name')->get(),
            'procedures' => Procedure::where('is_active', true)->orderBy('name')->get(),
            'teeth' => Tooth::where('is_active', true)->where('dentition_type', 'permanent')->orderBy('arch')->orderBy('position')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'lab_id' => ['required', 'exists:labs,id'],
            'procedure_id' => ['nullable', 'exists:procedures,id'],
            'tooth_id' => ['nullable', 'exists:teeth,id'],
            'expected_date' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
        $data['created_by'] = $request->user()->id;

        $order = LabOrder::create($data);

        return redirect()->route('lab-orders.index')->with('status', "Lab order {$order->case_number} created.");
    }

    public function transition(Request $request, LabOrder $labOrder): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(LabOrder::STATUSES)],
        ]);

        try {
            $labOrder->transitionTo($data['status']);
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('lab-orders.index')->with('status', 'Lab order updated.');
    }
}
