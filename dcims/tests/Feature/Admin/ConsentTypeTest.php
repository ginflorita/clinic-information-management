<?php

namespace Tests\Feature\Admin;

use App\Models\ConsentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsentTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_index(): void
    {
        $this->get(route('admin.consent-types.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_consent_type(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.consent-types.store'), [
                'name' => 'Extraction Consent',
                'description' => 'Consent for tooth extraction procedures.',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.consent-types.index'));
        $this->assertDatabaseHas('consent_types', ['name' => 'Extraction Consent', 'is_active' => true]);
    }

    public function test_authenticated_user_can_update_a_consent_type(): void
    {
        $consentType = ConsentType::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.consent-types.update', $consentType), [
                'name' => 'Updated Name',
            ])
            ->assertRedirect(route('admin.consent-types.index'));

        $this->assertDatabaseHas('consent_types', ['id' => $consentType->id, 'name' => 'Updated Name', 'is_active' => false]);
    }

    public function test_authenticated_user_can_delete_a_consent_type(): void
    {
        $consentType = ConsentType::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.consent-types.destroy', $consentType))
            ->assertRedirect(route('admin.consent-types.index'));

        $this->assertSoftDeleted($consentType);
    }
}
