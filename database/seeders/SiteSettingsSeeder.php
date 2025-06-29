<?php

namespace Database\Seeders;

use App\Models\SiteSettings;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SiteSettings::truncate();

        SiteSettings::create([
            'company_name' => 'KKP Security Project Tracker',
            'company_email' => 'admin@kkpsecurity.com',
            'company_phone' => '+1 (555) 123-4567',
            'company_address' => '123 Security Street, Business District, City, State 12345',
            'site_logo_url' => '/images/logo.png',
            'favicon_url' => '/images/favicon.ico',
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'api_keys' => [
                'mailgun' => [
                    'domain' => '',
                    'secret' => '',
                ],
                'stripe' => [
                    'public' => '',
                    'secret' => '',
                ],
                'google_maps' => '',
                'backup_webhook' => '',
            ],
            'maintenance_mode' => false,
        ]);
    }
}
