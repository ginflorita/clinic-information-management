<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'address_type' => 'home',
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => null,
            'barangay' => fake()->citySuffix(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => 'Philippines',
            'is_primary' => true,
        ];
    }
}
