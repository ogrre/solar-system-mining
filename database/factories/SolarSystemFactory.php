<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SolarSystem>
 */
class SolarSystemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resources = [
            'easy' => ['Iron', 'Copper', 'Silicon', 'Water'],
            'medium' => ['Gold', 'Platinum', 'Uranium', 'Crystals'],
            'hard' => ['Antimatter', 'Dark Matter', 'Quantum Crystals'],
            'extreme' => ['Neutronium', 'Temporal Crystals', 'Void Energy'],
        ];

        $difficulty = fake()->randomElement(['easy', 'medium', 'hard', 'extreme']);

        return [
            'name' => fake()->unique()->randomElement([
                'Alpha Centauri', 'Kepler-442', 'Wolf 1061', 'Proxima Centauri',
                'Gliese 667C', 'TRAPPIST-1', 'Ross 128', 'LHS 1140',
            ]),
            'description' => fake()->sentence(12),
            'difficulty' => $difficulty,
            'available_resources' => fake()->randomElements($resources[$difficulty], fake()->numberBetween(2, min(4, count($resources[$difficulty])))),
            'max_players' => match ($difficulty) {
                'easy' => 4,
                'medium' => fake()->numberBetween(4, 6),
                'hard' => fake()->numberBetween(6, 8),
                'extreme' => fake()->numberBetween(8, 10),
            },
            'min_players' => match ($difficulty) {
                'easy' => 1,
                'medium' => 2,
                'hard' => 3,
                'extreme' => 4,
            },
            'image_url' => null,
            'is_active' => true,
        ];
    }
}
