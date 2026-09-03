<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * This is the main seeder that orchestrates all other seeders.
     * Run with: php artisan db:seed
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();

        $this->call([
            SettingSeeder::class,    // App settings (office hours, location, etc.)
            RealisticSeeder::class,  // Demo data (users, interns, tasks, etc.)
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeding completed!');
    }
}
