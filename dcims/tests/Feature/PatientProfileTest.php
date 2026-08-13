<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\PatientAddress;
use App\Models\PatientContact;
use App\Models\PatientIdentifier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_address_can_be_added_to_a_patient(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.addresses.store', $patient), [
                'address_type' => 'home',
                'address_line_1' => '123 Rizal St',
                'city' => 'Manila',
                'is_primary' => '1',
            ])
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patient_addresses', [
            'patient_id' => $patient->id,
            'address_line_1' => '123 Rizal St',
            'is_primary' => true,
        ]);
    }

    public function test_an_address_can_be_removed(): void
    {
        $patient = Patient::factory()->create();
        $address = PatientAddress::factory()->create(['patient_id' => $patient->id]);

        $this->actingAs(User::factory()->create())
            ->delete(route('patients.addresses.destroy', [$patient, $address]))
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseMissing('patient_addresses', ['id' => $address->id]);
    }

    public function test_a_contact_can_be_added_to_a_patient(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.contacts.store', $patient), [
                'contact_type' => 'mobile',
                'contact_value' => '09171234567',
                'is_primary' => '1',
            ])
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patient_contacts', [
            'patient_id' => $patient->id,
            'contact_value' => '09171234567',
        ]);
    }

    public function test_a_contact_can_be_removed(): void
    {
        $patient = Patient::factory()->create();
        $contact = PatientContact::factory()->create(['patient_id' => $patient->id]);

        $this->actingAs(User::factory()->create())
            ->delete(route('patients.contacts.destroy', [$patient, $contact]))
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseMissing('patient_contacts', ['id' => $contact->id]);
    }

    public function test_an_identifier_can_be_added_to_a_patient(): void
    {
        $patient = Patient::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('patients.identifiers.store', $patient), [
                'identifier_type' => 'philhealth',
                'identifier_value' => '12-345678901-2',
            ])
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patient_identifiers', [
            'patient_id' => $patient->id,
            'identifier_value' => '12-345678901-2',
        ]);
    }

    public function test_an_identifier_can_be_removed(): void
    {
        $patient = Patient::factory()->create();
        $identifier = PatientIdentifier::factory()->create(['patient_id' => $patient->id]);

        $this->actingAs(User::factory()->create())
            ->delete(route('patients.identifiers.destroy', [$patient, $identifier]))
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseMissing('patient_identifiers', ['id' => $identifier->id]);
    }
}
