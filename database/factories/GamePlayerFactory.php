<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GamePlayer>
 */
class GamePlayerFactory extends Factory
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
            'user_id' => User::factory(),
            'status' => 'joined',
            'player_data' => [
                'role' => fake()->randomElement(['miner', 'engineer', 'scout', 'trader', 'security']),
                'experience_level' => fake()->numberBetween(1, 10),
                'specialization' => fake()->randomElement(['Asteroid Mining', 'Deep Core Drilling', 'Rare Metals']),
                'resources_contributed' => fake()->numberBetween(0, 5000),
                'missions_completed' => fake()->numberBetween(0, 20),
                'reputation_score' => fake()->numberBetween(30, 100),
            ],
            'joined_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'left_at' => null,
        ];
    }
}
