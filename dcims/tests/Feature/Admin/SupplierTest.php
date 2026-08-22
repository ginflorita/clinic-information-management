<?php

namespace Tests\Feature\Admin;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.suppliers.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_supplier(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.suppliers.store'), [
                'name' => 'Dental Supply Co',
                'contact_person' => 'Maria Santos',
                'email' => 'maria@dentalsupply.example',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'Dental Supply Co', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_supplier(): void
    {
        $supplier = Supplier::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.suppliers.update', $supplier), [
                'name' => 'Updated Supplier',
            ])
            ->assertRedirect(route('admin.suppliers.index'));

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'Updated Supplier', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.suppliers.destroy', $supplier))
            ->assertRedirect(route('admin.suppliers.index'));

        $this->assertSoftDeleted($supplier);
    }
}
