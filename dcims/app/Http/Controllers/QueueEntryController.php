<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\QueueEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueEntryController extends Controller
{
    public function index(): View
    {
        $today = now()->format('Y-m-d');

        return view('queue.index', [
            'queueEntries' => QueueEntry::with('patient')
                ->whereDate('queue_date', $today)
                ->whereNotIn('status', ['completed', 'skipped'])
                ->orderBy('queue_number')
                ->get(),
            'patients' => Patient::where('status', 'active')->orderBy('last_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
        ]);

        $today = now()->format('Y-m-d');

        QueueEntry::create([
            'patient_id' => $data['patient_id'],
            'queue_date' => $today,
            'queue_number' => QueueEntry::nextQueueNumberFor($today),
            'status' => 'waiting',
            'checked_in_at' => now(),
        ]);

        return redirect()->route('queue.index')->with('status', 'Patient added to queue.');
    }

    public function call(QueueEntry $queueEntry): RedirectResponse
    {
        $queueEntry->update(['status' => 'called', 'called_at' => now()]);

        return redirect()->route('queue.index')->with('status', 'Patient called.');
    }

    public function start(QueueEntry $queueEntry): RedirectResponse
    {
        $queueEntry->update(['status' => 'in_treatment', 'started_at' => now()]);

        return redirect()->route('queue.index')->with('status', 'Treatment started.');
    }

    public function complete(QueueEntry $queueEntry): RedirectResponse
    {
        $queueEntry->update(['status' => 'completed', 'completed_at' => now()]);

        return redirect()->route('queue.index')->with('status', 'Visit completed.');
    }

    public function skip(QueueEntry $queueEntry): RedirectResponse
    {
        $queueEntry->update(['status' => 'skipped']);

        return redirect()->route('queue.index')->with('status', 'Patient skipped.');
    }
}
