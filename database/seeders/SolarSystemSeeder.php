<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SolarSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $solarSystems = [
            [
                'name' => 'Alpha Centauri',
                'description' => 'A beginner-friendly system with abundant resources and peaceful mining conditions.',
                'difficulty' => 'easy',
                'available_resources' => ['Iron', 'Copper', 'Silicon', 'Water'],
                'max_players' => 4,
                'min_players' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Kepler-442',
                'description' => 'A balanced system offering moderate challenges with diverse resource distribution.',
                'difficulty' => 'medium',
                'available_resources' => ['Gold', 'Platinum', 'Uranium', 'Rare Metals', 'Crystals'],
                'max_players' => 6,
                'min_players' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Wolf 1061',
                'description' => 'A dangerous system with valuable resources protected by hostile forces.',
                'difficulty' => 'hard',
                'available_resources' => ['Antimatter', 'Dark Matter', 'Quantum Crystals', 'Exotic Matter'],
                'max_players' => 8,
                'min_players' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Proxima Centauri',
                'description' => 'The ultimate challenge for experienced miners. Extreme conditions, incredible rewards.',
                'difficulty' => 'extreme',
                'available_resources' => ['Neutronium', 'Temporal Crystals', 'Void Energy', 'Singularity Cores'],
                'max_players' => 10,
                'min_players' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Gliese 667C',
                'description' => 'A mysterious system with unique mining mechanics and ancient artifacts.',
                'difficulty' => 'medium',
                'available_resources' => ['Artifacts', 'Energy Cells', 'Biomatter', 'Nano Materials'],
                'max_players' => 5,
                'min_players' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($solarSystems as $system) {
            \App\Models\SolarSystem::create($system);
        }
    }
}
