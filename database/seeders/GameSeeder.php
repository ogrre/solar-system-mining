<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $solarSystems = \App\Models\SolarSystem::all();
        $users = \App\Models\User::all();

        if ($solarSystems->isEmpty() || $users->isEmpty()) {
            echo "⚠️ No solar systems or users found. Run SolarSystemSeeder and UserSeeder first.\n";

            return;
        }

        $gameNames = [
            'Operation Stardust Mining',
            'Nebula Resource Extraction',
            'Asteroid Belt Conquest',
            'Deep Space Excavation',
            'Quantum Crystal Hunt',
            'Void Mining Initiative',
            'Stellar Mining Company',
            'Galactic Resource Rush',
            'Cosmic Ore Expedition',
            'Space Mining Alliance',
            'Interstellar Drilling Co',
            'Planetary Mining Guild',
            'Solar Wind Harvesting',
            'Dark Matter Collection',
            'Antimatter Mining Corp',
            'Zero-G Mining Operations',
            'Orbital Resource Station',
            'Starship Mining Fleet',
            'Galaxy Mining Syndicate',
            'Universal Mining Union',
        ];

        $descriptions = [
            'Join us for an epic mining adventure across the cosmos!',
            'Seeking experienced miners for dangerous but rewarding expedition.',
            'New mining company looking for brave souls to explore uncharted territories.',
            'Advanced mining operation with state-of-the-art equipment.',
            'Casual mining group, all skill levels welcome.',
            'Hardcore mining expedition - only for veterans.',
            'Speed mining competition - fastest wins!',
            'Cooperative mining venture with shared profits.',
            'Research expedition combining mining with scientific discovery.',
            'Corporate mining operation with excellent benefits.',
            'Family-friendly mining adventure for beginners.',
            'Elite mining squad seeking top-tier operators.',
            'Stealth mining operation in contested space.',
            'Mining guild recruitment drive - join the best!',
            'Solo-friendly mining with optional group activities.',
            'PvP mining competition in dangerous sectors.',
            'Roleplay-heavy mining experience with rich storylines.',
            'Training program for new space miners.',
            'Veteran miners teaching advanced techniques.',
            'Emergency mining mission - immediate response needed.',
        ];

        $gamesCreated = 0;
        $faker = fake();

        foreach ($solarSystems as $system) {
            // Create 2-4 games per solar system
            $gameCount = $faker->numberBetween(2, 4);

            for ($i = 0; $i < $gameCount; $i++) {
                $host = $users->random();
                $gameName = $faker->randomElement($gameNames);
                $description = $faker->randomElement($descriptions);

                // Determine game status based on probability
                $statusRand = $faker->numberBetween(1, 100);
                if ($statusRand <= 40) {
                    $status = 'waiting';
                } elseif ($statusRand <= 70) {
                    $status = 'active';
                } elseif ($statusRand <= 85) {
                    $status = 'paused';
                } elseif ($statusRand <= 95) {
                    $status = 'completed';
                } else {
                    $status = 'abandoned';
                }

                // Set player count based on status
                if ($status === 'waiting') {
                    $currentPlayers = $faker->numberBetween(1, min(3, $system->max_players - 1));
                } elseif ($status === 'active') {
                    $currentPlayers = $faker->numberBetween(2, $system->max_players);
                } elseif ($status === 'paused') {
                    $currentPlayers = $faker->numberBetween(2, $system->max_players);
                } else {
                    $currentPlayers = $faker->numberBetween(1, $system->max_players);
                }

                // Set timestamps based on status
                $createdAt = now()->subDays($faker->numberBetween(1, 30));
                $startedAt = in_array($status, ['active', 'paused', 'completed', 'abandoned'])
                    ? $createdAt->copy()->addHours($faker->numberBetween(1, 48))
                    : null;
                $endedAt = in_array($status, ['completed', 'abandoned'])
                    ? $startedAt?->copy()->addDays($faker->numberBetween(1, 14))
                    : null;
                $lastActivity = match ($status) {
                    'waiting' => $createdAt->copy()->addHours($faker->numberBetween(1, 24)),
                    'active' => now()->subMinutes($faker->numberBetween(5, 1440)),
                    'paused' => now()->subHours($faker->numberBetween(2, 72)),
                    'completed', 'abandoned' => $endedAt,
                };

                $game = \App\Models\Game::create([
                    'solar_system_id' => $system->id,
                    'host_user_id' => $host->id,
                    'name' => $gameName,
                    'description' => $description,
                    'status' => $status,
                    'current_players' => $currentPlayers,
                    'game_settings' => [
                        'difficulty_modifier' => $faker->numberBetween(80, 120) / 100,
                        'resource_multiplier' => $faker->numberBetween(50, 200) / 100,
                        'time_limit_hours' => $faker->numberBetween(24, 168),
                        'allow_pvp' => $faker->boolean(),
                        'auto_save_interval' => $faker->numberBetween(5, 30),
                    ],
                    'game_state' => $status !== 'waiting' ? [
                        'total_resources_mined' => $faker->numberBetween(1000, 50000),
                        'active_mining_sites' => $faker->numberBetween(1, 5),
                        'completed_objectives' => $faker->numberBetween(0, 10),
                        'current_phase' => $faker->numberBetween(1, 5),
                    ] : null,
                    'started_at' => $startedAt,
                    'ended_at' => $endedAt,
                    'last_activity_at' => $lastActivity,
                    'is_public' => $faker->boolean(80), // 80% public games
                    'join_code' => strtoupper(\Illuminate\Support\Str::random(8)),
                    'created_at' => $createdAt,
                    'updated_at' => $lastActivity,
                ]);

                $gamesCreated++;
            }
        }

        echo "✅ Created {$gamesCreated} games across ".$solarSystems->count()." solar systems\n";
    }
}
