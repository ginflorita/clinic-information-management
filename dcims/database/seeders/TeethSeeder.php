<?php

namespace Database\Seeders;

use App\Models\Tooth;
use Illuminate\Database\Seeder;

class TeethSeeder extends Seeder
{
    /**
     * FDI (ISO 3950) two-digit notation: quadrant 1-8, position within quadrant.
     * Quadrants 1-4 = permanent (8 teeth each, 32 total).
     * Quadrants 5-8 = primary/deciduous (5 teeth each, 20 total).
     */
    public function run(): void
    {
        $permanentNames = [
            1 => 'Central Incisor',
            2 => 'Lateral Incisor',
            3 => 'Canine',
            4 => 'First Premolar',
            5 => 'Second Premolar',
            6 => 'First Molar',
            7 => 'Second Molar',
            8 => 'Third Molar',
        ];

        $primaryNames = [
            1 => 'Central Incisor',
            2 => 'Lateral Incisor',
            3 => 'Canine',
            4 => 'First Molar',
            5 => 'Second Molar',
        ];

        $quadrants = [
            1 => ['arch' => 'maxillary', 'dentition_type' => 'permanent', 'names' => $permanentNames],
            2 => ['arch' => 'maxillary', 'dentition_type' => 'permanent', 'names' => $permanentNames],
            3 => ['arch' => 'mandibular', 'dentition_type' => 'permanent', 'names' => $permanentNames],
            4 => ['arch' => 'mandibular', 'dentition_type' => 'permanent', 'names' => $permanentNames],
            5 => ['arch' => 'maxillary', 'dentition_type' => 'primary', 'names' => $primaryNames],
            6 => ['arch' => 'maxillary', 'dentition_type' => 'primary', 'names' => $primaryNames],
            7 => ['arch' => 'mandibular', 'dentition_type' => 'primary', 'names' => $primaryNames],
            8 => ['arch' => 'mandibular', 'dentition_type' => 'primary', 'names' => $primaryNames],
        ];

        foreach ($quadrants as $quadrant => $meta) {
            foreach ($meta['names'] as $position => $name) {
                Tooth::updateOrCreate(
                    [
                        'notation_system' => 'FDI',
                        'tooth_code' => "{$quadrant}{$position}",
                        'dentition_type' => $meta['dentition_type'],
                    ],
                    [
                        'tooth_name' => $name,
                        'arch' => $meta['arch'],
                        'position' => $position,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
