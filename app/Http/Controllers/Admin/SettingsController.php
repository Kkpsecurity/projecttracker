<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Constructor - Apply auth middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        // You can add admin role check here if needed
        // $this->middleware('role:admin');
    }

    /**
     * Display the site settings form
     *
     * @ai-command watch:model:SiteSettings
     * @ai-command suggest:run_migrations
     * If this fails, likely due to missing table "fresh_site_settings"
     */
    public function index()
    {
        $settings = SiteSettings::getInstance();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the site settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:1000',
            'site_logo_url' => 'nullable|url|max:500',
            'favicon_url' => 'nullable|url|max:500',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'maintenance_mode' => 'boolean',

            // API Keys validation
            'api_keys.mailgun.domain' => 'nullable|string|max:255',
            'api_keys.mailgun.secret' => 'nullable|string|max:255',
            'api_keys.stripe.public' => 'nullable|string|max:255',
            'api_keys.stripe.secret' => 'nullable|string|max:255',
            'api_keys.google_maps' => 'nullable|string|max:255',
            'api_keys.backup_webhook' => 'nullable|url|max:500',

            // File uploads
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon_file' => 'nullable|image|mimes:ico,png,jpg,gif|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $settings = SiteSettings::getInstance();
        $data = $request->only([
            'company_name',
            'company_email',
            'company_phone',
            'company_address',
            'primary_color',
            'secondary_color',
            'maintenance_mode'
        ]);

        // Handle file uploads
        if ($request->hasFile('logo_file')) {
            $logoPath = $request->file('logo_file')->store('public/uploads/site');
            $data['site_logo_url'] = Storage::url($logoPath);
        } elseif ($request->filled('site_logo_url')) {
            $data['site_logo_url'] = $request->site_logo_url;
        }

        if ($request->hasFile('favicon_file')) {
            $faviconPath = $request->file('favicon_file')->store('public/uploads/site');
            $data['favicon_url'] = Storage::url($faviconPath);
        } elseif ($request->filled('favicon_url')) {
            $data['favicon_url'] = $request->favicon_url;
        }

        // Handle API keys
        $apiKeys = [];
        if ($request->has('api_keys')) {
            $apiKeys = $request->input('api_keys');
            // Remove empty values to keep JSON clean
            $apiKeys = array_filter($apiKeys, function($value) {
                if (is_array($value)) {
                    return !empty(array_filter($value));
                }
                return !empty($value);
            });
        }
        $data['api_keys'] = $apiKeys;

        $settings->updateSettings($data);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Site settings updated successfully!');
    }

    /**
     * Reset settings to defaults
     */
    public function reset()
    {
        $settings = SiteSettings::getInstance();
        $settings->updateSettings([
            'company_name' => 'KKP Security Project Tracker',
            'company_email' => null,
            'company_phone' => null,
            'company_address' => null,
            'site_logo_url' => null,
            'favicon_url' => null,
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'api_keys' => [],
            'maintenance_mode' => false,
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings reset to defaults successfully!');
    }

    /**
     * Toggle maintenance mode
     */
    public function toggleMaintenance()
    {
        $settings = SiteSettings::getInstance();
        $newMode = !$settings->maintenance_mode;

        $settings->updateSettings(['maintenance_mode' => $newMode]);

        $message = $newMode ? 'Maintenance mode enabled.' : 'Maintenance mode disabled.';

        return redirect()->route('admin.settings.index')
            ->with('success', $message);
    }
}
