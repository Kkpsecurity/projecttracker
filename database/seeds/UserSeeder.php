<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks for PostgreSQL

        // Truncate the users table
        User::truncate();

        // Create user records
        User::create([
            'name' => 'Richard Clark',
            'email' => 'richievc@gmail.com',
            'password' => bcrypt('Secure$101'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Chris Jones',
            'email' => 'jonesy@cisworldservices.org',
            'password' => bcrypt('Secure$101'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Craig Gundry',
            'email' => 'gundrycs@cisadmin.com',
            'password' => bcrypt('Secure$101'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'KC Poulin',
            'email' => 'poulinkc@cisadmin.com',
            'password' => bcrypt('Secure$101'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Ashley Casey',
            'email' => 'ashley@s2institute.com',
            'password' => bcrypt('Secure$101'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Hector Rodriguez',
            'email' => 'rodrighb@cisworldservices.org',
            'password' => bcrypt('Secure$101'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Sandra Gundry',
            'email' => 'sgundry@s2institute.com',
            'password' => bcrypt('Secure$101'),
            'email_verified_at' => now(),
        ]);


    }
}
