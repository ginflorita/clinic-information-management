<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Referral;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use LogicException;

class ReferralController extends Controller
{
    public function index(): View
    {
        return view('referrals.index', [
            'referrals' => Referral::with(['patient', 'referringProvider'])->orderByDesc('referral_date')->get(),
        ]);
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'referring_provider_id' => ['required', 'exists:providers,id'],
            'receiving_name' => ['required', 'string', 'max:255'],
            'receiving_specialty' => ['nullable', 'string', 'max:255'],
            'receiving_contact' => ['nullable', 'string', 'max:255'],
            'reason' => ['required', 'string'],
            'clinical_summary' => ['nullable', 'string'],
            'referral_date' => ['required', 'date'],
        ]);
        $data['created_by'] = $request->user()->id;

        $patient->referrals()->create($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Referral created.');
    }

    public function transition(Request $request, Referral $referral): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Referral::STATUSES)],
            'response' => ['nullable', 'string'],
        ]);

        try {
            $referral->transitionTo($data['status'], $data['response'] ?? null);
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('referrals.index')->with('status', 'Referral updated.');
    }
}
