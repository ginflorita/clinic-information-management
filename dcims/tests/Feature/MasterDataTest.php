<?php

namespace Tests\Feature;

use App\Models\Tooth;
use Database\Seeders\TeethSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_master_tables_exist_after_migration(): void
    {
        foreach ([
            'procedure_categories',
            'procedures',
            'tooth_conditions',
            'teeth',
            'providers',
            'chairs',
            'appointment_types',
            'payment_methods',
            'inventory_categories',
            'inventory_units',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing table: {$table}");
        }
    }

    public function test_teeth_seeder_populates_permanent_and_primary_sets(): void
    {
        $this->seed(TeethSeeder::class);

        $this->assertSame(52, Tooth::count());
        $this->assertSame(32, Tooth::where('dentition_type', 'permanent')->count());
        $this->assertSame(20, Tooth::where('dentition_type', 'primary')->count());

        // Spot-check a known FDI code from the architecture reference (§71 example).
        $this->assertDatabaseHas('teeth', [
            'notation_system' => 'FDI',
            'tooth_code' => '36',
            'tooth_name' => 'First Molar',
            'dentition_type' => 'permanent',
            'arch' => 'mandibular',
        ]);
    }

    public function test_teeth_seeder_is_idempotent(): void
    {
        $this->seed(TeethSeeder::class);
        $this->seed(TeethSeeder::class);

        $this->assertSame(52, Tooth::count());
    }
}
