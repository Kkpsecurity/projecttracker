<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SafeMigrateCommand extends Command
{
    protected $signature = 'migrate:safe {--force : Run without interactive confirmation} {--no-backup : Skip backup step (not recommended)}';

    protected $description = 'Run migrations with safety prompts and an automatic backup step.';

    public function handle(): int
    {
        $dbName = (string) config('database.connections.'.config('database.default').'.database', env('DB_DATABASE'));

        if (! $this->option('force')) {
            $this->warn('You are about to run database migrations.');
            $this->line('Database: '.$dbName);

            if (! $this->confirm('Proceed?', false)) {
                $this->info('Aborted.');
                return self::SUCCESS;
            }
        }

        if (! $this->option('no-backup')) {
            $this->info('Creating backup...');

            // Prefer the existing SQL backup command if available.
            try {
                $exitCode = Artisan::call('db:backup');
                $this->output->write(Artisan::output());

                if ($exitCode !== self::SUCCESS) {
                    $this->error('Backup command failed.');

                    if (! $this->option('force') && ! $this->confirm('Continue anyway?', false)) {
                        $this->info('Aborted.');
                        return self::FAILURE;
                    }
                }
            } catch (\Throwable $e) {
                $this->error('Backup step failed: '.$e->getMessage());

                if (! $this->option('force') && ! $this->confirm('Continue anyway?', false)) {
                    $this->info('Aborted.');
                    return self::FAILURE;
                }
            }
        } else {
            $this->warn('Skipping backup step due to --no-backup.');
        }

        $this->info('Running migrations...');

        $exitCode = Artisan::call('migrate', [
            '--force' => true,
        ]);

        $this->output->write(Artisan::output());

        if ($exitCode !== self::SUCCESS) {
            $this->error('Migrations failed.');
            $this->line('If needed, restore from a backup in: storage/backups');
            return self::FAILURE;
        }

        $this->info('✅ Migrations completed successfully.');
        return self::SUCCESS;
    }
}
