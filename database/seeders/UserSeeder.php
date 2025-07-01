<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This creates admin-only accounts with role-based permissions:
     * - superadmin: Full system access
     * - manager: Project and user management
     * - editor: Content editing and data entry
     * - auditor: Read-only access for compliance
     */
    public function run(): void
    {
        // For PostgreSQL, we'll use CASCADE delete instead of disabling constraints
        // Delete all users (foreign key constraints will be handled by CASCADE)
        User::query()->delete();

        // Reset the sequence for PostgreSQL
        DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), 1, false);");

        // Define users with role-based access (all are admins with different permissions)
        $users = [

            [
                'name' => 'Richard Clark',
                'email' => 'richievc@gmail.com',
                'role' => 'superadmin',
            ],
            [
                'name' => 'Chris Jones',
                'email' => 'jonesy@cisworldservices.org',
                'role' => 'superadmin',
            ],
            [
                'name' => 'Craig Gundry',
                'email' => 'gundrycs@cisadmin.com',
                'role' => 'manager',
            ],
            [
                'name' => 'KC Poulin',
                'email' => 'poulinkc@cisadmin.com',
                'role' => 'editor',
            ],
            [
                'name' => 'Ashley Casey',
                'email' => 'ashley@s2institute.com',
                'role' => 'manager',
            ],
            [
                'name' => 'Hector Rodriguez',
                'email' => 'rodrighb@cisworldservices.org',
                'role' => 'manager',
            ],
            [
                'name' => 'Sandra Gundry',
                'email' => 'sgundry@s2institute.com',
                'role' => 'auditor',
            ],
        ];

        // Create each user with standard admin privileges
        foreach ($users as $data) {
            User::create([
                ...$data,
                'password' => Hash::make('Secure$101'),
                'email_verified_at' => now(),
                'is_admin' => true,  // Everyone is an admin (this is an admin-only system)
                'is_active' => true,
            ]);
        }

        $this->command->info('Admin user accounts seeded successfully.');
        $this->command->info('All users are admins with role-based permissions:');
        $this->command->info('- superadmin: Full system access');
        $this->command->info('- manager: Project and user management');
        $this->command->info('- editor: Content editing and data entry');
        $this->command->info('- auditor: Read-only access for compliance');
    }
}
