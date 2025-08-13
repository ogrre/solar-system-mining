<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GamePlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = \App\Models\Game::all();
        $users = \App\Models\User::all();

        if ($games->isEmpty() || $users->isEmpty()) {
            echo "⚠️ No games or users found. Run GameSeeder and UserSeeder first.\n";

            return;
        }

        $playersCreated = 0;
        $faker = fake();

        foreach ($games as $game) {
            // Always add the host as the first player
            $hostJoinedAt = $game->created_at->copy()->addMinutes($faker->numberBetween(1, 30));

            \App\Models\GamePlayer::create([
                'game_id' => $game->id,
                'user_id' => $game->host_user_id,
                'status' => 'joined',
                'joined_at' => $hostJoinedAt,
                'player_data' => [
                    'role' => 'host',
                    'experience_level' => $faker->numberBetween(1, 10),
                    'specialization' => collect(['Mining', 'Engineering', 'Combat', 'Trading', 'Exploration'])->random(),
                    'resources_contributed' => $faker->numberBetween(0, 5000),
                    'missions_completed' => $faker->numberBetween(0, 20),
                    'reputation_score' => $faker->numberBetween(50, 100),
                ],
            ]);
            $playersCreated++;

            // Add additional players up to current_players count
            $additionalPlayers = $game->current_players - 1;
            $availableUsers = $users->where('id', '!=', $game->host_user_id);

            if ($additionalPlayers > 0 && $availableUsers->count() > 0) {
                $selectedUsers = $availableUsers->random(min($additionalPlayers, $availableUsers->count()));

                foreach ($selectedUsers as $user) {
                    $joinedAt = $hostJoinedAt->copy()->addHours($faker->numberBetween(1, 48));
                    $status = 'joined';
                    $leftAt = null;

                    // Some players might have left (for completed/abandoned games)
                    if (in_array($game->status, ['completed', 'abandoned']) && $faker->boolean(20)) {
                        $status = 'left';
                        $leftAt = $joinedAt->copy()->addDays($faker->numberBetween(1, 10));
                    }

                    \App\Models\GamePlayer::create([
                        'game_id' => $game->id,
                        'user_id' => $user->id,
                        'status' => $status,
                        'joined_at' => $joinedAt,
                        'left_at' => $leftAt,
                        'player_data' => [
                            'role' => collect(['miner', 'engineer', 'scout', 'trader', 'security'])->random(),
                            'experience_level' => $faker->numberBetween(1, 10),
                            'specialization' => collect(['Asteroid Mining', 'Deep Core Drilling', 'Rare Metals', 'Gas Harvesting', 'Crystal Mining'])->random(),
                            'resources_contributed' => $faker->numberBetween(0, 3000),
                            'missions_completed' => $faker->numberBetween(0, 15),
                            'reputation_score' => $faker->numberBetween(30, 95),
                            'join_reason' => collect([
                                'Looking for adventure',
                                'Need credits',
                                'Recommended by friend',
                                'Training opportunity',
                                'Regular team member',
                            ])->random(),
                        ],
                    ]);
                    $playersCreated++;
                }
            }
        }

        echo "✅ Created {$playersCreated} game players across ".$games->count()." games\n";
    }
}
