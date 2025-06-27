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
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the users table
        User::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create user records
        $users = [
            [
                'name' => 'Richard Clark',
                'email' => 'richievc@gmail.com',
                'password' => Hash::make('Secure$101'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Chris Jones',
                'email' => 'jonesy@cisworldservices.org',
                'password' => Hash::make('Secure$101'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Craig Gundry',
                'email' => 'gundrycs@cisadmin.com',
                'password' => Hash::make('Secure$101'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'KC Poulin',
                'email' => 'poulinkc@cisadmin.com',
                'password' => Hash::make('Secure$101'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ashley Casey',
                'email' => 'ashley@s2institute.com',
                'password' => Hash::make('Secure$101'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Hector Rodriguez',
                'email' => 'rodrighb@cisworldservices.org',
                'password' => Hash::make('Secure$101'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sandra Gundry',
                'email' => 'sgundry@s2institute.com',
                'password' => Hash::make('Secure$101'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('User data seeded successfully.');
    }
}
