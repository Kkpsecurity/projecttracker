<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSettings;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            [
                'key' => 'company_name',
                'value' => 'KKP Security Project Tracker',
                'type' => 'string',
                'group' => 'company',
                'description' => 'Company name displayed throughout the application'
            ],
            [
                'key' => 'company_email',
                'value' => 'admin@kkpsecurity.com',
                'type' => 'string',
                'group' => 'company',
                'description' => 'Primary company email address'
            ],
            [
                'key' => 'company_phone',
                'value' => '+1 (555) 123-4567',
                'type' => 'string',
                'group' => 'company',
                'description' => 'Company phone number'
            ],
            [
                'key' => 'company_address',
                'value' => '123 Security Street, Business District',
                'type' => 'string',
                'group' => 'company',
                'description' => 'Company physical address'
            ],
            [
                'key' => 'site_logo_url',
                'value' => '/images/logo.png',
                'type' => 'string',
                'group' => 'branding',
                'description' => 'URL to the site logo image'
            ],
            [
                'key' => 'favicon_url',
                'value' => '/images/favicon.ico',
                'type' => 'string',
                'group' => 'branding',
                'description' => 'URL to the site favicon'
            ],
            [
                'key' => 'primary_color',
                'value' => '#007bff',
                'type' => 'string',
                'group' => 'branding',
                'description' => 'Primary brand color'
            ],
            [
                'key' => 'secondary_color',
                'value' => '#6c757d',
                'type' => 'string',
                'group' => 'branding',
                'description' => 'Secondary brand color'
            ],
            [
                'key' => 'api_keys',
                'value' => json_encode([
                    'mailgun' => ['domain' => '', 'secret' => ''],
                    'stripe' => ['public' => '', 'secret' => ''],
                    'google_maps' => '',
                    'backup_webhook' => ''
                ]),
                'type' => 'json',
                'group' => 'api',
                'description' => 'API keys and integration settings'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable/disable maintenance mode'
            ]
        ];

        foreach ($defaultSettings as $setting) {
            SiteSettings::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
