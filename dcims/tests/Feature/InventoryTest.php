<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_inventory(): void
    {
        $this->get(route('inventory.index'))->assertRedirect(route('login'));
    }

    public function test_receiving_stock_creates_a_batch_and_a_stock_in_movement(): void
    {
        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('inventory.batches.store', $product), [
                'supplier_id' => $supplier->id,
                'batch_number' => 'B-100',
                'quantity' => 50,
                'unit_cost' => 2.5,
                'received_at' => now()->toDateString(),
            ]);

        $response->assertRedirect(route('inventory.show', $product));
        $this->assertDatabaseHas('product_batches', ['product_id' => $product->id, 'batch_number' => 'B-100', 'quantity' => 50]);
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'movement_type' => 'stock_in', 'quantity' => 50]);
    }

    public function test_recording_usage_reduces_the_batch_and_logs_a_stock_out_movement(): void
    {
        $product = Product::factory()->create();
        $batch = ProductBatch::factory()->create(['product_id' => $product->id, 'quantity' => 30]);

        $this->actingAs(User::factory()->create())
            ->post(route('inventory.stock-out', $product), [
                'batch_id' => $batch->id,
                'quantity' => 10,
            ])
            ->assertRedirect(route('inventory.show', $product));

        $this->assertDatabaseHas('product_batches', ['id' => $batch->id, 'quantity' => 20]);
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'batch_id' => $batch->id, 'movement_type' => 'stock_out', 'quantity' => -10]);
    }

    public function test_recording_usage_cannot_exceed_the_batchs_available_quantity(): void
    {
        $product = Product::factory()->create();
        $batch = ProductBatch::factory()->create(['product_id' => $product->id, 'quantity' => 5]);

        $this->actingAs(User::factory()->create())
            ->post(route('inventory.stock-out', $product), [
                'batch_id' => $batch->id,
                'quantity' => 10,
            ])
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('product_batches', ['id' => $batch->id, 'quantity' => 5]);
    }

    public function test_an_adjustment_can_correct_a_batchs_quantity_up_or_down(): void
    {
        $product = Product::factory()->create();
        $batch = ProductBatch::factory()->create(['product_id' => $product->id, 'quantity' => 20]);

        $this->actingAs(User::factory()->create())
            ->post(route('inventory.adjust', $product), [
                'batch_id' => $batch->id,
                'delta' => -3,
                'notes' => 'Damaged in storage',
            ])
            ->assertRedirect(route('inventory.show', $product));

        $this->assertDatabaseHas('product_batches', ['id' => $batch->id, 'quantity' => 17]);
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'movement_type' => 'adjustment', 'quantity' => -3, 'notes' => 'Damaged in storage']);
    }

    public function test_an_adjustment_cannot_take_a_batch_below_zero(): void
    {
        $product = Product::factory()->create();
        $batch = ProductBatch::factory()->create(['product_id' => $product->id, 'quantity' => 5]);

        $this->actingAs(User::factory()->create())
            ->post(route('inventory.adjust', $product), [
                'batch_id' => $batch->id,
                'delta' => -10,
                'notes' => 'Count correction',
            ])
            ->assertSessionHasErrors('delta');

        $this->assertDatabaseHas('product_batches', ['id' => $batch->id, 'quantity' => 5]);
    }

    public function test_an_adjustment_requires_a_reason(): void
    {
        $product = Product::factory()->create();
        $batch = ProductBatch::factory()->create(['product_id' => $product->id, 'quantity' => 5]);

        $this->actingAs(User::factory()->create())
            ->post(route('inventory.adjust', $product), [
                'batch_id' => $batch->id,
                'delta' => 2,
            ])
            ->assertSessionHasErrors('notes');
    }

    public function test_a_batch_belonging_to_another_product_is_rejected(): void
    {
        $product = Product::factory()->create();
        $otherProductsBatch = ProductBatch::factory()->create(['quantity' => 20]);

        $this->actingAs(User::factory()->create())
            ->post(route('inventory.stock-out', $product), [
                'batch_id' => $otherProductsBatch->id,
                'quantity' => 1,
            ])
            ->assertSessionHasErrors('batch_id');
    }

    public function test_the_index_flags_products_below_their_reorder_level(): void
    {
        $lowStock = Product::factory()->create(['name' => 'Low Stock Gloves', 'reorder_level' => 10]);
        ProductBatch::factory()->create(['product_id' => $lowStock->id, 'quantity' => 2]);

        $response = $this->actingAs(User::factory()->create())->get(route('inventory.index'));

        $response->assertOk();
        $response->assertViewHas('lowStockCount', 1);
    }

    public function test_the_show_page_lists_batches_and_movements(): void
    {
        $product = Product::factory()->create();
        $batch = ProductBatch::factory()->create(['product_id' => $product->id, 'batch_number' => 'B-777', 'quantity' => 10]);

        $response = $this->actingAs(User::factory()->create())->get(route('inventory.show', $product));

        $response->assertOk();
        $response->assertSee('B-777');
    }
}
