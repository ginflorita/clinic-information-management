<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolePermissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'role_id' => Role::factory(),
            'module' => fake()->randomElement(array_keys(Role::MODULES)),
        ];
    }
}
