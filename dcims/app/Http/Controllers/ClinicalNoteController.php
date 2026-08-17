<?php

namespace App\Http\Controllers;

use App\Models\ClinicalNote;
use App\Models\Encounter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClinicalNoteController extends Controller
{
    public function store(Request $request, Encounter $encounter): RedirectResponse
    {
        $data = $request->validate([
            'note_type' => ['required', 'string', 'max:100'],
            'note_text' => ['required', 'string'],
        ]);
        $data['created_by'] = $request->user()->id;

        $encounter->clinicalNotes()->create($data);

        return redirect()->route('encounters.show', $encounter)->with('status', 'Note added.');
    }

    public function update(Request $request, Encounter $encounter, ClinicalNote $note): RedirectResponse
    {
        if ($note->status !== 'draft') {
            throw ValidationException::withMessages([
                'note_text' => 'Only a draft note can be edited — sign it or create an amendment instead.',
            ]);
        }

        $data = $request->validate([
            'note_type' => ['required', 'string', 'max:100'],
            'note_text' => ['required', 'string'],
        ]);

        $note->update($data);

        return redirect()->route('encounters.show', $encounter)->with('status', 'Note updated.');
    }

    public function sign(Request $request, Encounter $encounter, ClinicalNote $note): RedirectResponse
    {
        if ($note->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => 'Only a draft note can be signed.',
            ]);
        }

        $note->sign($request->user());

        return redirect()->route('encounters.show', $encounter)->with('status', 'Note signed.');
    }

    public function amend(Request $request, Encounter $encounter, ClinicalNote $note): RedirectResponse
    {
        if ($note->status !== 'signed') {
            throw ValidationException::withMessages([
                'status' => 'Only a signed note can be amended.',
            ]);
        }

        $data = $request->validate([
            'note_text' => ['required', 'string'],
            'amendment_reason' => ['required', 'string'],
        ]);

        $note->amend($data['note_text'], $data['amendment_reason'], $request->user());

        return redirect()->route('encounters.show', $encounter)->with('status', 'Amendment recorded.');
    }
}
