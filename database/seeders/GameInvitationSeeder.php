<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GameInvitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = \App\Models\Game::whereIn('status', ['waiting', 'active'])->get();
        $users = \App\Models\User::all();

        if ($games->isEmpty() || $users->isEmpty()) {
            echo "⚠️ No games or users found. Run GameSeeder and UserSeeder first.\n";

            return;
        }

        $invitationMessages = [
            'Join us for an epic mining adventure!',
            'We need skilled miners for our next expedition.',
            'Your expertise would be valuable to our team.',
            'Looking for a reliable team member.',
            'Want to join our mining crew?',
            'Perfect mining opportunity for you!',
            'We could use your skills on this mission.',
            'Interested in some profitable mining?',
            'Join our space mining operation!',
            'Great mining spot - want to join?',
            'Exclusive invitation to join our team.',
            'Mining expedition needs experienced pilots.',
            'Special invitation for veteran miners.',
            'Join our elite mining squad.',
            'Perfect mission for your skill level.',
        ];

        $invitationsCreated = 0;
        $faker = fake();

        foreach ($games as $game) {
            // Skip games that are already full
            /** @var \App\Models\SolarSystem $solarSystem */
            $solarSystem = $game->solarSystem;
            if ($game->current_players >= $solarSystem->max_players) {
                continue;
            }

            // Get users who aren't already in this game
            $gamePlayerIds = \App\Models\GamePlayer::where('game_id', $game->id)
                ->where('status', 'joined')
                ->pluck('user_id')
                ->toArray();

            $availableUsers = $users->whereNotIn('id', $gamePlayerIds);

            if ($availableUsers->isEmpty()) {
                continue;
            }

            // Create 0-3 invitations per game
            $invitationCount = $faker->numberBetween(0, 3);

            if ($invitationCount > 0) {
                $selectedUsers = $availableUsers->random(min($invitationCount, $availableUsers->count()));

                foreach ($selectedUsers as $user) {
                    $createdAt = $game->created_at->copy()->addHours($faker->numberBetween(1, 72));

                    // Determine invitation status
                    $statusRand = $faker->numberBetween(1, 100);
                    if ($statusRand <= 60) {
                        $status = 'pending';
                        $respondedAt = null;
                    } elseif ($statusRand <= 80) {
                        $status = 'accepted';
                        $respondedAt = $createdAt->copy()->addHours($faker->numberBetween(1, 24));
                    } else {
                        $status = 'declined';
                        $respondedAt = $createdAt->copy()->addHours($faker->numberBetween(1, 48));
                    }

                    \App\Models\GameInvitation::create([
                        'game_id' => $game->id,
                        'inviter_user_id' => $game->host_user_id,
                        'invited_user_id' => $user->id,
                        'status' => $status,
                        'message' => $faker->randomElement($invitationMessages),
                        'responded_at' => $respondedAt,
                        'expires_at' => $createdAt->copy()->addDays(7),
                        'created_at' => $createdAt,
                        'updated_at' => $respondedAt ?: $createdAt,
                    ]);

                    $invitationsCreated++;
                }
            }
        }

        echo "✅ Created {$invitationsCreated} game invitations\n";
    }
}
