<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_purchase_orders(): void
    {
        $this->get(route('purchase-orders.index'))->assertRedirect(route('login'));
    }

    public function test_a_purchase_order_can_be_created(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('purchase-orders.store'), [
                'supplier_id' => $supplier->id,
                'order_date' => now()->toDateString(),
            ]);

        $po = PurchaseOrder::first();
        $response->assertRedirect(route('purchase-orders.show', $po));

        $this->assertNotNull($po);
        $this->assertSame('draft', $po->status);
        $this->assertMatchesRegularExpression('/^PO-\d{4}-\d{6}$/', $po->po_number);
    }

    public function test_items_can_be_added_while_draft(): void
    {
        $po = PurchaseOrder::factory()->create(['status' => 'draft']);
        $product = Product::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('purchase-orders.items.store', $po), [
                'product_id' => $product->id,
                'quantity_ordered' => 20,
                'unit_cost' => 3.5,
            ])
            ->assertRedirect(route('purchase-orders.show', $po));

        $this->assertDatabaseHas('purchase_order_items', [
            'purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'quantity_ordered' => 20,
        ]);
    }

    public function test_items_cannot_be_added_once_ordered(): void
    {
        $po = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $product = Product::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('purchase-orders.items.store', $po), [
                'product_id' => $product->id,
                'quantity_ordered' => 20,
                'unit_cost' => 3.5,
            ])
            ->assertSessionHasErrors('product_id');
    }

    public function test_a_draft_order_with_no_items_cannot_be_marked_ordered(): void
    {
        $po = PurchaseOrder::factory()->create(['status' => 'draft']);

        $this->actingAs(User::factory()->create())
            ->post(route('purchase-orders.transition', $po), ['status' => 'ordered'])
            ->assertSessionHasErrors('status');

        $this->assertSame('draft', $po->fresh()->status);
    }

    public function test_a_draft_order_with_items_can_be_marked_ordered(): void
    {
        $po = PurchaseOrder::factory()->create(['status' => 'draft']);
        PurchaseOrderItem::factory()->create(['purchase_order_id' => $po->id]);

        $this->actingAs(User::factory()->create())
            ->post(route('purchase-orders.transition', $po), ['status' => 'ordered']);

        $this->assertSame('ordered', $po->fresh()->status);
    }

    public function test_receiving_goods_creates_a_batch_and_a_stock_movement(): void
    {
        $product = Product::factory()->create();
        $po = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'quantity_ordered' => 30,
            'unit_cost' => 4,
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('purchase-orders.receipts.store', $po), [
                'received_date' => now()->toDateString(),
                'items' => [$item->id => 30],
            ])
            ->assertRedirect(route('purchase-orders.show', $po));

        $this->assertDatabaseHas('purchase_order_items', ['id' => $item->id, 'quantity_received' => 30]);
        $this->assertDatabaseHas('product_batches', ['product_id' => $product->id, 'quantity' => 30, 'unit_cost' => 4]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'movement_type' => 'stock_in',
            'quantity' => 30,
            'reference_type' => 'purchase_order',
            'reference_id' => $po->id,
        ]);
        $this->assertSame('received', $po->fresh()->status);
    }

    public function test_a_partial_receipt_leaves_the_order_partially_received(): void
    {
        $po = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'quantity_ordered' => 30,
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('purchase-orders.receipts.store', $po), [
                'received_date' => now()->toDateString(),
                'items' => [$item->id => 10],
            ]);

        $this->assertSame('partially_received', $po->fresh()->status);
        $this->assertSame('10.00', $item->fresh()->quantity_received);
    }

    public function test_receiving_more_than_the_remaining_quantity_is_rejected(): void
    {
        $po = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'quantity_ordered' => 10,
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('purchase-orders.receipts.store', $po), [
                'received_date' => now()->toDateString(),
                'items' => [$item->id => 15],
            ])
            ->assertSessionHasErrors('items');

        $this->assertSame('0.00', $item->fresh()->quantity_received);
    }

    public function test_goods_cannot_be_received_against_a_draft_order(): void
    {
        $po = PurchaseOrder::factory()->create(['status' => 'draft']);
        $item = PurchaseOrderItem::factory()->create(['purchase_order_id' => $po->id, 'quantity_ordered' => 10]);

        $this->actingAs(User::factory()->create())
            ->post(route('purchase-orders.receipts.store', $po), [
                'received_date' => now()->toDateString(),
                'items' => [$item->id => 5],
            ])
            ->assertSessionHasErrors('received_date');
    }

    public function test_purchase_orders_have_no_hard_delete_route(): void
    {
        $po = PurchaseOrder::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('purchase-orders.show', $po))
            ->assertStatus(405);

        $this->assertDatabaseHas('purchase_orders', ['id' => $po->id]);
    }
}
