<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameInvitation>
 */
class GameInvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'inviter_user_id' => User::factory(),
            'invited_user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'accepted', 'declined']),
            'message' => fake()->sentence(),
            'expires_at' => fake()->dateTimeBetween('now', '+7 days'),
            'responded_at' => fake()->optional(0.6)->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
