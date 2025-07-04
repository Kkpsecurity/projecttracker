<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSettings extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    private static $instance = null;
    private $settings = [];

    /**
     * Get singleton instance with all settings loaded
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->loadSettings();
        }

        return self::$instance;
    }

    /**
     * Load all settings from database with caching
     */
    private function loadSettings()
    {
        $this->settings = Cache::remember('site_settings', 3600, function () {
            $settings = [];

            foreach (self::all() as $setting) {
                $value = $setting->value;

                // Cast values based on type
                switch ($setting->type) {
                    case 'boolean':
                        $value = (bool) $value;
                        break;
                    case 'integer':
                        $value = (int) $value;
                        break;
                    case 'json':
                        $value = json_decode($value, true) ?: [];
                        break;
                    default:
                        $value = (string) $value;
                }

                $settings[$setting->key] = $value;
            }

            return $settings;
        });
    }

    /**
     * Get setting value
     */
    public function __get($key)
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }

        return $this->getDefaultValue($key);
    }

    /**
     * Check if setting exists
     */
    public function __isset($key)
    {
        return isset($this->settings[$key]) || $this->hasDefaultValue($key);
    }

    /**
     * Get default values for settings
     */
    private function getDefaultValue($key)
    {
        $defaults = [
            'company_name' => 'KKP Security Project Tracker',
            'company_email' => 'admin@kkpsecurity.com',
            'company_phone' => '+1 (555) 123-4567',
            'company_address' => '123 Security Street, Business District',
            'site_logo_url' => '/images/logo.png',
            'favicon_url' => '/images/favicon.ico',
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'api_keys' => [],
            'maintenance_mode' => false,
        ];

        return $defaults[$key] ?? null;
    }

    /**
     * Check if key has default value
     */
    private function hasDefaultValue($key)
    {
        $defaults = [
            'company_name',
            'company_email',
            'company_phone',
            'company_address',
            'site_logo_url',
            'favicon_url',
            'primary_color',
            'secondary_color',
            'api_keys',
            'maintenance_mode'
        ];

        return in_array($key, $defaults);
    }

    /**
     * Update setting value
     */
    public function updateSetting($key, $value, $type = 'string', $group = 'general')
    {
        $setting = self::firstOrCreate(['key' => $key], [
            'type' => $type,
            'group' => $group
        ]);

        // Prepare value based on type
        switch ($type) {
            case 'boolean':
                $value = $value ? '1' : '0';
                break;
            case 'json':
                $value = json_encode($value);
                break;
            default:
                $value = (string) $value;
        }

        $setting->update(['value' => $value]);

        // Clear cache to reload settings
        Cache::forget('site_settings');
        $this->loadSettings();

        return true;
    }

    /**
     * Update multiple settings at once
     */
    public function updateSettings($data)
    {
        foreach ($data as $key => $value) {
            // Determine type based on key or value
            $type = 'string';
            if ($key === 'maintenance_mode') {
                $type = 'boolean';
            } elseif ($key === 'api_keys') {
                $type = 'json';
            }

            $this->updateSetting($key, $value, $type);
        }

        return true;
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        Cache::forget('site_settings');
    }
}
