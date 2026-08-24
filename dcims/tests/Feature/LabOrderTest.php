<?php

namespace Tests\Feature;

use App\Models\Lab;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_lab_orders(): void
    {
        $this->get(route('lab-orders.index'))->assertRedirect(route('login'));
    }

    public function test_a_lab_order_can_be_created(): void
    {
        $patient = Patient::factory()->create();
        $lab = Lab::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('lab-orders.store'), [
                'patient_id' => $patient->id,
                'lab_id' => $lab->id,
                'expected_date' => now()->addWeek()->toDateString(),
                'cost' => 1500,
            ]);

        $response->assertRedirect(route('lab-orders.index'));

        $order = LabOrder::first();
        $this->assertNotNull($order);
        $this->assertSame($patient->id, $order->patient_id);
        $this->assertSame($lab->id, $order->lab_id);
        $this->assertSame('pending', $order->status);
        $this->assertMatchesRegularExpression('/^LAB-\d{4}-\d{6}$/', $order->case_number);
    }

    public function test_a_lab_order_moves_through_the_full_status_sequence_and_stamps_dates(): void
    {
        $order = LabOrder::factory()->create();
        $user = User::factory()->create();

        $this->assertNull($order->sent_date);

        $this->actingAs($user)->post(route('lab-orders.transition', $order), ['status' => 'sent']);
        $order->refresh();
        $this->assertSame('sent', $order->status);
        $this->assertNotNull($order->sent_date);

        $this->actingAs($user)->post(route('lab-orders.transition', $order), ['status' => 'in_progress']);
        $this->assertSame('in_progress', $order->fresh()->status);

        $this->actingAs($user)->post(route('lab-orders.transition', $order), ['status' => 'ready']);
        $this->assertSame('ready', $order->fresh()->status);

        $this->assertNull($order->received_date);
        $this->actingAs($user)->post(route('lab-orders.transition', $order), ['status' => 'received']);
        $order->refresh();
        $this->assertSame('received', $order->status);
        $this->assertNotNull($order->received_date);
    }

    public function test_a_pending_lab_order_cannot_jump_directly_to_ready(): void
    {
        $order = LabOrder::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('lab-orders.transition', $order), ['status' => 'ready'])
            ->assertSessionHasErrors('status');

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_a_pending_lab_order_can_be_cancelled(): void
    {
        $order = LabOrder::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('lab-orders.transition', $order), ['status' => 'cancelled'])
            ->assertRedirect(route('lab-orders.index'));

        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_a_received_lab_order_cannot_transition_further(): void
    {
        $order = LabOrder::factory()->create(['status' => 'received', 'received_date' => now()->toDateString()]);

        $this->actingAs(User::factory()->create())
            ->post(route('lab-orders.transition', $order), ['status' => 'sent'])
            ->assertSessionHasErrors('status');
    }
}
