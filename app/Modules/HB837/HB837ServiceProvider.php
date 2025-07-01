<?php

namespace App\Modules\HB837;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class HB837ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register module services
        $this->app->singleton('hb837.service', function ($app) {
            return new Services\HB837Service();
        });

        $this->app->singleton('hb837.upload', function ($app) {
            return new Services\UploadService();
        });

        $this->app->singleton('hb837.import', function ($app) {
            return new Services\ImportService();
        });

        $this->app->singleton('hb837.export', function ($app) {
            return new Services\ExportService();
        });

        // Register services in container for dependency injection
        $this->app->bind(Services\HB837Service::class);
        $this->app->bind(Services\UploadService::class);
        $this->app->bind(Services\ImportService::class);
        $this->app->bind(Services\ExportService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load module routes
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // Load module views
        $this->loadViewsFrom(resource_path('views/modules/hb837'), 'hb837');

        // Load module config
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'hb837');

        // Publish module assets if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config.php' => config_path('modules/hb837.php'),
            ], 'hb837-config');
        }
    }
}
