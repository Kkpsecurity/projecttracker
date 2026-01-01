<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏗️  Starting project data seeding...');
        
        // Seed in proper order (users first, then clients)
        $this->call([
            UsersTableSeeder::class,
            ClientsTableSeeder::class,
        ]);
        
        $this->command->info('🎉 Project data seeding completed successfully!');
        $this->command->info('📊 Database now contains full project context for HB837 testing');
    }
}