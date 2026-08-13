<?php

namespace Tests\Feature\Admin;

use App\Models\AppointmentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.appointment-types.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_an_appointment_type(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.appointment-types.store'), [
                'name' => 'Cleaning',
                'default_duration_minutes' => 30,
                'color' => '#0d6efd',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.appointment-types.index'));
        $this->assertDatabaseHas('appointment_types', ['name' => 'Cleaning', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_an_appointment_type(): void
    {
        $type = AppointmentType::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.appointment-types.update', $type), [
                'name' => 'Updated Name',
                'default_duration_minutes' => $type->default_duration_minutes,
            ])
            ->assertRedirect(route('admin.appointment-types.index'));

        $this->assertDatabaseHas('appointment_types', ['id' => $type->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_an_appointment_type(): void
    {
        $type = AppointmentType::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.appointment-types.destroy', $type))
            ->assertRedirect(route('admin.appointment-types.index'));

        $this->assertSoftDeleted($type);
    }
}
