<?php

namespace Tests\Feature;

use App\Models\ClinicalNote;
use App\Models\Encounter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_note_can_be_added_to_an_encounter_as_a_draft(): void
    {
        $encounter = Encounter::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.notes.store', $encounter), [
                'note_type' => 'progress',
                'note_text' => 'Patient reports mild sensitivity.',
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));

        $this->assertDatabaseHas('clinical_notes', [
            'encounter_id' => $encounter->id,
            'note_text' => 'Patient reports mild sensitivity.',
            'status' => 'draft',
        ]);
    }

    public function test_a_draft_note_can_be_edited(): void
    {
        $encounter = Encounter::factory()->create();
        $note = ClinicalNote::factory()->create(['encounter_id' => $encounter->id, 'status' => 'draft']);

        $response = $this->actingAs(User::factory()->create())
            ->put(route('encounters.notes.update', [$encounter, $note]), [
                'note_type' => $note->note_type,
                'note_text' => 'Corrected while still draft.',
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));
        $this->assertSame('Corrected while still draft.', $note->fresh()->note_text);
    }

    public function test_a_draft_note_can_be_signed(): void
    {
        $encounter = Encounter::factory()->create();
        $note = ClinicalNote::factory()->create(['encounter_id' => $encounter->id, 'status' => 'draft']);
        $signer = User::factory()->create();

        $response = $this->actingAs($signer)
            ->post(route('encounters.notes.sign', [$encounter, $note]));

        $response->assertRedirect(route('encounters.show', $encounter));

        $note->refresh();
        $this->assertSame('signed', $note->status);
        $this->assertSame($signer->id, $note->signed_by);
        $this->assertNotNull($note->signed_at);
    }

    public function test_an_already_signed_note_cannot_be_signed_again(): void
    {
        $encounter = Encounter::factory()->create();
        $note = ClinicalNote::factory()->create(['encounter_id' => $encounter->id, 'status' => 'signed']);

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.notes.sign', [$encounter, $note]))
            ->assertSessionHasErrors('status');
    }

    public function test_a_signed_note_cannot_be_edited_directly(): void
    {
        $encounter = Encounter::factory()->create();
        $note = ClinicalNote::factory()->create([
            'encounter_id' => $encounter->id,
            'status' => 'signed',
            'note_text' => 'Original signed text',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->put(route('encounters.notes.update', [$encounter, $note]), [
                'note_type' => $note->note_type,
                'note_text' => 'Sneaky silent overwrite',
            ]);

        $response->assertSessionHasErrors('note_text');
        $this->assertSame('Original signed text', $note->fresh()->note_text);
    }

    public function test_a_signed_note_can_be_amended_and_the_original_text_is_preserved(): void
    {
        $encounter = Encounter::factory()->create();
        $note = ClinicalNote::factory()->create([
            'encounter_id' => $encounter->id,
            'status' => 'signed',
            'note_text' => 'Original signed text',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('encounters.notes.amend', [$encounter, $note]), [
                'note_text' => 'Amended text with the correction',
                'amendment_reason' => 'Typo in diagnosis',
            ]);

        $response->assertRedirect(route('encounters.show', $encounter));

        $note->refresh();
        $this->assertSame('amended', $note->status);
        $this->assertSame('Original signed text', $note->note_text, 'The original row must never be overwritten.');

        $amendment = ClinicalNote::where('amends_note_id', $note->id)->first();
        $this->assertNotNull($amendment);
        $this->assertSame('draft', $amendment->status);
        $this->assertSame('Amended text with the correction', $amendment->note_text);
        $this->assertSame('Typo in diagnosis', $amendment->amendment_reason);
    }

    public function test_a_draft_note_cannot_be_amended(): void
    {
        $encounter = Encounter::factory()->create();
        $note = ClinicalNote::factory()->create(['encounter_id' => $encounter->id, 'status' => 'draft']);

        $this->actingAs(User::factory()->create())
            ->post(route('encounters.notes.amend', [$encounter, $note]), [
                'note_text' => 'Should not be allowed',
                'amendment_reason' => 'N/A',
            ])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseCount('clinical_notes', 1);
    }

    public function test_an_amended_note_cannot_be_edited_directly(): void
    {
        $encounter = Encounter::factory()->create();
        $note = ClinicalNote::factory()->create([
            'encounter_id' => $encounter->id,
            'status' => 'amended',
            'note_text' => 'Superseded text',
        ]);

        $this->actingAs(User::factory()->create())
            ->put(route('encounters.notes.update', [$encounter, $note]), [
                'note_type' => $note->note_type,
                'note_text' => 'Should not overwrite',
            ])
            ->assertSessionHasErrors('note_text');

        $this->assertSame('Superseded text', $note->fresh()->note_text);
    }
}
