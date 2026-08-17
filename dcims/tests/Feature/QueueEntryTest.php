<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\QueueEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_patient_can_be_added_to_the_queue(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('queue.store'), ['patient_id' => $patient->id]);

        $response->assertRedirect(route('queue.index'));

        $this->assertDatabaseHas('queue_entries', [
            'patient_id' => $patient->id,
            'queue_number' => 1,
            'status' => 'waiting',
        ]);
    }

    public function test_queue_numbers_increment_within_the_same_day(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('queue.store'), ['patient_id' => Patient::factory()->create()->id]);
        $this->actingAs($user)->post(route('queue.store'), ['patient_id' => Patient::factory()->create()->id]);

        $this->assertSame([1, 2], QueueEntry::orderBy('queue_number')->pluck('queue_number')->all());
    }

    public function test_queue_entry_moves_through_call_start_complete(): void
    {
        $entry = QueueEntry::factory()->create(['status' => 'waiting']);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('queue.call', $entry))->assertRedirect(route('queue.index'));
        $this->assertSame('called', $entry->fresh()->status);
        $this->assertNotNull($entry->fresh()->called_at);

        $this->actingAs($user)->post(route('queue.start', $entry))->assertRedirect(route('queue.index'));
        $this->assertSame('in_treatment', $entry->fresh()->status);

        $this->actingAs($user)->post(route('queue.complete', $entry))->assertRedirect(route('queue.index'));
        $this->assertSame('completed', $entry->fresh()->status);
        $this->assertNotNull($entry->fresh()->completed_at);
    }

    public function test_a_patient_can_be_skipped(): void
    {
        $entry = QueueEntry::factory()->create(['status' => 'waiting']);

        $this->actingAs(User::factory()->create())
            ->post(route('queue.skip', $entry))
            ->assertRedirect(route('queue.index'));

        $this->assertSame('skipped', $entry->fresh()->status);
    }

    public function test_completed_and_skipped_entries_do_not_appear_in_the_active_queue(): void
    {
        QueueEntry::factory()->create(['status' => 'completed', 'queue_date' => now()->format('Y-m-d')]);
        QueueEntry::factory()->create(['status' => 'skipped', 'queue_date' => now()->format('Y-m-d')]);
        $waiting = QueueEntry::factory()->create(['status' => 'waiting', 'queue_date' => now()->format('Y-m-d')]);

        $response = $this->actingAs(User::factory()->create())->get(route('queue.index'));

        $response->assertOk();
        $response->assertViewHas('queueEntries', fn ($entries) => $entries->pluck('id')->all() === [$waiting->id]);
    }
}
