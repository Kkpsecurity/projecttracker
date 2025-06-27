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

        // Option 1: Use PostgreSQL migration data (from actual dump)
        if ($this->command->confirm('Do you want to migrate data from PostgreSQL dump?', false)) {
            $this->call([
                PostgreSQLToMySQLDataSeeder::class,
            ]);
            $this->command->info('✅ PostgreSQL data migration completed!');
            return;
        }

        // Option 2: Use sample/test data (original seeders)
        $this->command->info('Using sample/test data seeders...');
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
