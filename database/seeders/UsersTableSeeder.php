<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding users table with consultant data...');

        $users = array (
  0 => 
  array (
    'id' => 3,
    'name' => 'KC Poulin',
    'email' => 'poulinkc@cisadmin.com',
    'email_verified_at' => '2021-06-03 14:02:14',
    'password' => '$2y$10$P8vA4kMMulvxwI1o76Xl2Oa.5leOXmVjottFe27qdH7LLNtSAG5TS',
    'remember_token' => NULL,
    'created_at' => '2021-06-03 14:02:14',
    'updated_at' => '2021-06-03 14:02:14',
  ),
  1 => 
  array (
    'id' => 1,
    'name' => 'Richard Clark',
    'email' => 'richievc@gmail.com',
    'email_verified_at' => '2021-06-03 14:02:13',
    'password' => '$2y$10$TETBzuocQV7U.EwZoIRW0.IYDK4LnWQYpGOsXqZhuvmBvV9aulR2C',
    'remember_token' => NULL,
    'created_at' => '2021-06-03 14:02:13',
    'updated_at' => '2021-06-03 14:18:51',
  ),
  2 => 
  array (
    'id' => 4,
    'name' => 'Jeff Ezell',
    'email' => 'ezelljt@kkpsecuritygroup.com',
    'email_verified_at' => '2021-06-03 14:02:14',
    'password' => '$2y$10$SQlT/yGleZCGGrPfksYlyeH/v69NLNhyZxCihbq8R6LDRt4szYRtq',
    'remember_token' => 'U968mrbJI3vgDrOoXSPEV6bhyL76S2Hy4EYmh4sT5hdpCyk5BcGnhMW8WASD',
    'created_at' => '2021-06-03 14:02:14',
    'updated_at' => '2021-06-03 14:02:14',
  ),
  3 => 
  array (
    'id' => 6,
    'name' => 'Ashley Casey',
    'email' => 'ashley@s2institute.com',
    'email_verified_at' => '2022-11-21 11:25:21',
    'password' => '$2y$10$i2Cx5PzfZOeDpeGaRGB.T.6TI8ePnJsshwlwt0iTpo1TJ5uqZxNDu',
    'remember_token' => '',
    'created_at' => '2022-11-21 11:25:21',
    'updated_at' => '2022-11-22 15:26:34',
  ),
  4 => 
  array (
    'id' => 5,
    'name' => 'Hector Rodriguez',
    'email' => 'rodrighb@cisworldservices.org',
    'email_verified_at' => '2022-11-21 11:24:24',
    'password' => '$2y$10$fZULNLYUWoLk6SVg6uZC0.jO6wE8e8AWPM58qi1LvXg5aRgHeHIYW',
    'remember_token' => '11sGi4yg4k0XuSLZqvj0tzOhGC3bobOFtoFXpYwePLgh8ZXfU2ylMXJD37Bb',
    'created_at' => '2022-11-21 11:24:24',
    'updated_at' => '2022-11-22 15:26:51',
  ),
  5 => 
  array (
    'id' => 2,
    'name' => 'Craig Gundry',
    'email' => 'gundrycs@cisadmin.com',
    'email_verified_at' => '2021-06-03 14:02:13',
    'password' => '$2y$10$VoaJR61vGx2nb70081BoSeftaC/JlMq2lDxzNYVkgVlQu7UE4gdJO',
    'remember_token' => 'zoA0y73lGAuw4qC3Rz3nB8KhIF5Q7xymx3bceyR2xLGd5d9Slctfd0yjBtHC',
    'created_at' => '2021-06-03 14:02:13',
    'updated_at' => '2022-11-22 15:23:14',
  ),
  6 => 
  array (
    'id' => 7,
    'name' => 'Chris Jones',
    'email' => 'jonesy@cisworldservices.org',
    'email_verified_at' => NULL,
    'password' => '$2y$10$.kolArc6/VJVCS1YRRm3Lu5T8FttqoZVGFaY9lBHeALB1zWu39OeS',
    'remember_token' => NULL,
    'created_at' => '2023-01-26 18:53:48',
    'updated_at' => '2023-01-26 18:53:48',
  ),
);

        foreach ($users as $user) {
            // Check if user with this email already exists
            if (!DB::table('users')->where('email', $user['email'])->exists()) {
                DB::table('users')->insert($user);
            }
        }

        $this->command->info('✅ Seeded ' . count($users) . ' consultants successfully');
    }
}