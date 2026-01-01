<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Consultant;
use App\Models\HB837;
use App\Models\Plot;
use App\Models\PlotAddress;
use App\Models\SiteSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin users and other seed data
        $this->call(UserSeeder::class);
        $this->call(ConsultantSeeder::class);
        $this->call(SiteSettingsSeeder::class);
    }
}
