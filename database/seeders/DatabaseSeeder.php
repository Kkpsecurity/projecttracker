<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // Seed in order to handle relationships
        $this->call([
            UserSeeder::class,
            OwnerSeeder::class,
            ConsultantSeeder::class,
            ClientSeeder::class,
            PlotSeeder::class,
            HB837Seeder::class,
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
    }
}
