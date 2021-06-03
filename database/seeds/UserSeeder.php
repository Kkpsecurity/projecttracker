<?php

use App\User;
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
        DB::table('users')->truncate();

        User::create([
            'name' => 'Richard Clark',
            'email' => 'richievc@gmail.com',
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
            'name' => 'Jeff Ezell',
            'email' => 'ezelljt@kkpsecuritygroup.com',
            'password' => bcrypt('Secure$101'),
            'email_verified_at' => now(),
        ]);
    }
}
