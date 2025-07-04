<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DatabaseBackUp extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Backup the database and keep only the latest 5 backups';

    public function handle()
    {
        $this->info('Starting database backup...');

        $backupDir = storage_path('backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $backupFile = $backupDir.'/backup-'.now()->format('Y-m-d-H-i-s').'.sql';

        $command = sprintf(
            'mysqldump -u%s -p\'%s\' %s > %s',
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DATABASE'),
            $backupFile
        );

        $result = null;
        system($command, $result);

        if ($result !== 0) {
            $this->error('Backup failed.');

            return Command::FAILURE;
        }

        $this->info("Database backed up: {$backupFile}");

        // Rotate: Keep only the latest 5
        $files = collect(File::files($backupDir))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        if ($files->count() > 5) {
            $oldFiles = $files->slice(5);
            foreach ($oldFiles as $file) {
                File::delete($file->getRealPath());
                $this->info("Deleted old backup: {$file->getFilename()}");
            }
        }

        return Command::SUCCESS;
    }
}
