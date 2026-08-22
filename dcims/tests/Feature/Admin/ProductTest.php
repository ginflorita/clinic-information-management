<?php

namespace Tests\Feature\Admin;

use App\Models\InventoryCategory;
use App\Models\InventoryUnit;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.products.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_product(): void
    {
        $category = InventoryCategory::factory()->create();
        $unit = InventoryUnit::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.products.store'), [
                'sku' => 'GLV-001',
                'name' => 'Nitrile Gloves',
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'reorder_level' => 20,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['sku' => 'GLV-001', 'name' => 'Nitrile Gloves', 'is_active' => true]);
    }

    public function test_sku_must_be_unique(): void
    {
        Product::factory()->create(['sku' => 'GLV-001']);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.products.store'), [
                'sku' => 'GLV-001',
                'name' => 'Duplicate',
                'reorder_level' => 0,
            ])
            ->assertSessionHasErrors('sku');
    }

    public function test_authenticated_user_can_update_a_product(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.products.update', $product), [
                'sku' => $product->sku,
                'name' => 'Updated Name',
                'reorder_level' => 15,
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_product(): void
    {
        $product = Product::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted($product);
    }
}
