<?php

namespace Tests\Feature\Admin;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.payment-methods.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_payment_method(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.payment-methods.store'), [
                'name' => 'GCash',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.payment-methods.index'));
        $this->assertDatabaseHas('payment_methods', ['name' => 'GCash', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_payment_method(): void
    {
        $method = PaymentMethod::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.payment-methods.update', $method), ['name' => 'Updated Name'])
            ->assertRedirect(route('admin.payment-methods.index'));

        $this->assertDatabaseHas('payment_methods', ['id' => $method->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_payment_method(): void
    {
        $method = PaymentMethod::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.payment-methods.destroy', $method))
            ->assertRedirect(route('admin.payment-methods.index'));

        $this->assertSoftDeleted($method);
    }
}
