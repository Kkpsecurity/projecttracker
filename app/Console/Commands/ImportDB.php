<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportDB extends Command
{
    protected $signature = 'importdb:all-tables
                            {--path=database/seeds/data : Path to the JSON files}
                            {--include=* : Import only these tables}
                            {--skip=* : Skip these tables}';

    protected $description = 'Import JSON files into their respective database tables';

    public function handle()
    {
        $importPath = $this->option('path');
        $includeTables = $this->option('include');
        $skipTables = $this->option('skip');

        // Get all JSON files in the directory
        $files = Storage::disk('local')->files($importPath);

        foreach ($files as $file) {
            $tableName = pathinfo($file, PATHINFO_FILENAME);

            // Skip tables based on options
            if (! empty($includeTables) && ! in_array($tableName, $includeTables)) {
                continue;
            }
            if (in_array($tableName, $skipTables)) {
                continue;
            }

            $this->info("Importing data into table: $tableName");

            // Read JSON file
            $jsonData = Storage::disk('local')->get($file);
            $data = json_decode($jsonData, true);

            if (empty($data)) {
                $this->warn("No data found in $file.");

                continue;
            }

            // Insert data into the table
            DB::table($tableName)->insert($data);

            $this->info("Imported data into $tableName from $file");
        }

        $this->info('All tables imported successfully.');
    }
}
