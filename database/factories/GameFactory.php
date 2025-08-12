<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'solar_system_id' => \App\Models\SolarSystem::factory(),
            'host_user_id' => \App\Models\User::factory(),
            'name' => fake()->words(3, true).' Mining Operation',
            'description' => fake()->sentence(8),
            'status' => fake()->randomElement(['waiting', 'active', 'paused']),
            'current_players' => fake()->numberBetween(1, 4),
            'game_settings' => null,
            'game_state' => null,
            'started_at' => null,
            'ended_at' => null,
            'last_activity_at' => now(),
            'is_public' => fake()->boolean(80), // 80% chance of being public
            'join_code' => strtoupper(fake()->lexify('????????')),
        ];
    }
}
