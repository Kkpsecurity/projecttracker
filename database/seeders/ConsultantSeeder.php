<?php

namespace Database\Seeders;

use App\Models\Consultant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConsultantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Disable foreign key checks
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
         
         // Truncate the table (Optional: Only if you want a fresh import)
         Consultant::truncate();
         
         // Re-enable foreign key checks
         DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Path to the JSON file containing consultant data
         $jsonFile = storage_path('app/database/seeds/data/consultants.json');

         // Check if the file exists
         if (!File::exists($jsonFile)) {
             $this->command->error("File not found: $jsonFile");
             return;
         }

         // Read and decode JSON
         $jsonData = json_decode(File::get($jsonFile), true);

         if (!$jsonData) {
             $this->command->error("Invalid JSON format in $jsonFile");
             return;
         }

        // Insert each consultant into the database
         foreach ($jsonData as $data) {
            Consultant::create([
                'id' => $data['id'] ?? null,
                'first_name' => $data['first_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'email' => $data['email'] ?? null,
                'dba_company_name' => $data['dba_company_name'] ?? null,
                'mailing_address' => $data['mailing_address'] ?? null,
                'fcp_expiration_date' => $data['fcp_expiration_date'] ?? null,
                'assigned_light_meter' => $data['assigned_light_meter'] ?? null,
                'lm_nist_expiration_date' => $data['lm_nist_expiration_date'] ?? null,
                'subcontractor_bonus_rate' => $data['subcontractor_bonus_rate'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_at' => Carbon::parse($data['created_at'] ?? now()),
                'updated_at' => Carbon::parse($data['updated_at'] ?? now()),
             ]);
         }

        $this->command->info("Consultant data imported successfully from $jsonFile.");
    }
}
