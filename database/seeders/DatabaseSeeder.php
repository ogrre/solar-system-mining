<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "🚀 Starting database seeding...\n\n";

        $this->call([
            SolarSystemSeeder::class,
            UserSeeder::class,
            GameSeeder::class,
            GamePlayerSeeder::class,
            GameInvitationSeeder::class,
        ]);

        echo "\n🎉 Database seeding completed successfully!\n";
    }
}
