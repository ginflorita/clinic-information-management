<?php

namespace Tests\Feature;

use App\Models\Procedure;
use App\Models\Tooth;
use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreatmentPlanItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_add_an_item(): void
    {
        $plan = TreatmentPlan::factory()->create();

        $this->post(route('treatment-plans.items.store', $plan))->assertRedirect(route('login'));
    }

    public function test_an_item_can_be_added_with_an_explicit_unit_price(): void
    {
        $plan = TreatmentPlan::factory()->create();
        $procedure = Procedure::factory()->create(['default_fee' => 1000]);
        $tooth = Tooth::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('treatment-plans.items.store', $plan), [
                'procedure_id' => $procedure->id,
                'tooth_id' => $tooth->id,
                'quantity' => 2,
                'estimated_unit_price' => 1500,
                'priority' => 'high',
            ]);

        $response->assertRedirect(route('treatment-plans.show', $plan));

        $item = $plan->items()->first();
        $this->assertSame(2, $item->quantity);
        $this->assertEquals(1500, $item->estimated_unit_price);
        $this->assertEquals(3000, $item->estimated_total);
        $this->assertSame('high', $item->priority);
        $this->assertSame('proposed', $item->status);
    }

    public function test_unit_price_defaults_to_the_procedures_default_fee(): void
    {
        $plan = TreatmentPlan::factory()->create();
        $procedure = Procedure::factory()->create(['default_fee' => 850]);

        $this->actingAs(User::factory()->create())
            ->post(route('treatment-plans.items.store', $plan), [
                'procedure_id' => $procedure->id,
                'quantity' => 1,
            ]);

        $item = $plan->items()->first();
        $this->assertEquals(850, $item->estimated_unit_price);
        $this->assertEquals(850, $item->estimated_total);
    }

    public function test_an_item_status_can_be_updated(): void
    {
        $plan = TreatmentPlan::factory()->create();
        $item = TreatmentPlanItem::factory()->create(['treatment_plan_id' => $plan->id, 'status' => 'proposed']);

        $this->actingAs(User::factory()->create())
            ->patch(route('treatment-plans.items.status', [$plan, $item]), ['status' => 'accepted']);

        $this->assertSame('accepted', $item->fresh()->status);
    }

    public function test_completing_an_item_preserves_its_planning_data(): void
    {
        $plan = TreatmentPlan::factory()->create();
        $item = TreatmentPlanItem::factory()->create([
            'treatment_plan_id' => $plan->id,
            'status' => 'accepted',
            'quantity' => 2,
            'estimated_unit_price' => 1200,
            'estimated_total' => 2400,
            'notes' => 'Upper right quadrant',
        ]);

        $this->actingAs(User::factory()->create())
            ->patch(route('treatment-plans.items.status', [$plan, $item]), ['status' => 'completed']);

        $item->refresh();
        $this->assertSame('completed', $item->status);
        $this->assertSame(2, $item->quantity);
        $this->assertEquals(1200, $item->estimated_unit_price);
        $this->assertEquals(2400, $item->estimated_total);
        $this->assertSame('Upper right quadrant', $item->notes);
        $this->assertDatabaseCount('treatment_plan_items', 1);
    }
}
