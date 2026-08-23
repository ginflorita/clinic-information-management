<?php

namespace Tests\Feature\Admin;

use App\Models\Lab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.labs.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_lab(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.labs.store'), [
                'name' => 'Precision Dental Lab',
                'contact_person' => 'Alex Reyes',
                'phone' => '555-0199',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.labs.index'));
        $this->assertDatabaseHas('labs', ['name' => 'Precision Dental Lab', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_lab(): void
    {
        $lab = Lab::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.labs.update', $lab), [
                'name' => 'Updated Lab Name',
            ])
            ->assertRedirect(route('admin.labs.index'));

        $this->assertDatabaseHas('labs', ['id' => $lab->id, 'name' => 'Updated Lab Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_lab(): void
    {
        $lab = Lab::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.labs.destroy', $lab))
            ->assertRedirect(route('admin.labs.index'));

        $this->assertSoftDeleted($lab);
    }
}
