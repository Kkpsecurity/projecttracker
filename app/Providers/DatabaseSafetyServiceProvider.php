<?php

namespace App\Providers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class DatabaseSafetyServiceProvider extends ServiceProvider
{
    /**
     * Commands that can destroy schema/data and require explicit opt-in.
     */
    private const BLOCKED_COMMANDS = [
        'migrate:fresh',
        'migrate:reset',
        'migrate:refresh',
    ];

    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        Event::listen(CommandStarting::class, function (CommandStarting $event): void {
            $name = (string) ($event->command ?? '');

            if (! in_array($name, self::BLOCKED_COMMANDS, true)) {
                return;
            }

            $allowed = filter_var(env('DB_ALLOW_FRESH', false), FILTER_VALIDATE_BOOL);
            
            // Check for override key
            $overrideKey = env('DB_FRESH_OVERRIDE_KEY');
            if ($overrideKey === 'TmasterTM$101') {
                return;
            }

            if ($allowed) {
                return;
            }

            $dbName = (string) config('database.connections.'.config('database.default').'.database', env('DB_DATABASE'));

            $message = implode("\n", [
                '🚨 DANGER: '.$name.' is BLOCKED by safety protocol',
                '',
                'This command can DROP TABLES and/or LOSE DATA.',
                '',
                'If you need normal migrations, use:',
                '  php artisan migrate:safe',
                '',
                'To override (emergency only):',
                '  Set DB_ALLOW_FRESH=true in .env, run your command, then set it back to false.',
                '  OR set DB_FRESH_OVERRIDE_KEY=TmasterTM$101 in .env',
                '',
                'Current database: '.$dbName,
                'OPERATION BLOCKED FOR YOUR PROTECTION',
            ]);

            throw new RuntimeException($message);
        });
    }
}
