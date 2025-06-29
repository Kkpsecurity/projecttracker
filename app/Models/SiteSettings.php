<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSettings extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'site_logo_url',
        'favicon_url',
        'primary_color',
        'secondary_color',
        'api_keys',
        'maintenance_mode',
    ];

    protected $casts = [
        'api_keys' => 'array',
        'maintenance_mode' => 'boolean',
    ];

    /**
     * Get the singleton instance of site settings
     */
    public static function getInstance()
    {
        return Cache::remember('site_settings', 3600, function () {
            return self::first() ?? self::create([
                'company_name' => 'KKP Security Project Tracker',
                'primary_color' => '#007bff',
                'secondary_color' => '#6c757d',
            ]);
        });
    }

    /**
     * Update settings and clear cache
     */
    public function updateSettings(array $data)
    {
        $this->update($data);
        Cache::forget('site_settings');
        return $this;
    }

    /**
     * Get a specific setting value
     */
    public static function get($key, $default = null)
    {
        $settings = self::getInstance();
        return $settings->$key ?? $default;
    }

    /**
     * Get API key by name
     */
    public static function getApiKey($keyName, $default = null)
    {
        $settings = self::getInstance();
        return $settings->api_keys[$keyName] ?? $default;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('site_settings');
        });

        static::deleted(function () {
            Cache::forget('site_settings');
        });
    }
}
