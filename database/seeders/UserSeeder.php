<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin/Test users - Using hardcoded password for development/testing only
        // In production, use environment variables or secure password generation
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@solar-mining.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'jb@test.com'],
            [
                'name' => 'Jean-Baptiste Test',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Space-themed realistic users
        $spaceUsers = [
            ['name' => 'Captain Nova Sterling', 'email' => 'nova.sterling@spacefleet.com'],
            ['name' => 'Commander Rex Asteroid', 'email' => 'rex.asteroid@mining.corp'],
            ['name' => 'Dr. Luna Cosmic', 'email' => 'luna.cosmic@research.space'],
            ['name' => 'Pilot Zara Nebula', 'email' => 'zara.nebula@transport.space'],
            ['name' => 'Engineer Orion Steel', 'email' => 'orion.steel@engineering.space'],
            ['name' => 'Miner Jake Rockwell', 'email' => 'jake.rockwell@miners.guild'],
            ['name' => 'Trader Vera Stardust', 'email' => 'vera.stardust@trading.post'],
            ['name' => 'Scout Kai Horizon', 'email' => 'kai.horizon@exploration.space'],
            ['name' => 'Mechanic Finn Quantum', 'email' => 'finn.quantum@repair.station'],
            ['name' => 'Navigator Sage Cosmos', 'email' => 'sage.cosmos@navigation.space'],
            ['name' => 'Security Chief Max Iron', 'email' => 'max.iron@security.space'],
            ['name' => 'Medic Elena Starlight', 'email' => 'elena.starlight@medical.space'],
            ['name' => 'Analyst River Data', 'email' => 'river.data@analytics.space'],
            ['name' => 'Captain Storm Void', 'email' => 'storm.void@fleet.command'],
            ['name' => 'Engineer Maya Circuit', 'email' => 'maya.circuit@tech.space'],
        ];

        foreach ($spaceUsers as $userData) {
            \App\Models\User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
        }

        // Additional random users for testing
        \App\Models\User::factory(10)->create();

        echo '✅ Created '.\App\Models\User::count()." users\n";
    }
}
