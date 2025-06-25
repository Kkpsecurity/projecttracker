<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;


class ExportDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exportdb:all-tables
                            {--overwrite : Overwrite existing files}
                            {--include=* : Export only these tables}
                            {--skip=* : Skip these tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all database tables to JSON files for seeding purposes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Query PostgreSQL for all table names
        $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");

        // Create directory if it doesn't exist
        $exportPath = 'database/seeds/data';
        if (!Storage::disk('local')->exists($exportPath)) {
            Storage::disk('local')->makeDirectory($exportPath);
        }

        foreach ($tables as $table) {
            $tableName = $table->table_name;

            // Skip migrations table
            if ($tableName === 'migrations') {
                continue;
            }

            $this->info("Exporting table: $tableName");

            // Initialize an empty array to hold the data
            $data = [];

            // Check if the table has an 'id' column
            if (Schema::hasColumn($tableName, 'id')) {
                // Process records in chunks ordered by 'id'
                DB::table($tableName)->orderBy('id')->chunk(1000, function ($rows) use (&$data) {
                    foreach ($rows as $row) {
                        $data[] = (array) $row;
                    }
                });
            } else {
                // Retrieve the first column name
                $firstColumnName = Schema::getColumnListing($tableName)[0];

                // Process records in chunks ordered by the first column
                DB::table($tableName)->orderBy($firstColumnName)->chunk(1000, function ($rows) use (&$data) {
                    foreach ($rows as $row) {
                        $data[] = (array) $row;
                    }
                });
            }

            // Convert data to JSON
            $jsonData = json_encode($data, JSON_PRETTY_PRINT);

            // Save to file
            $fileName = "$exportPath/{$tableName}.json";
            Storage::disk('local')->put($fileName, $jsonData);

            $this->info("Exported $tableName to $fileName");
        }

        $this->info('All tables exported successfully.');
        return Command::SUCCESS;
    }
}
