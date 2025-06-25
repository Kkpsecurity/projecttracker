<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the table (Optional: Only if you want a fresh import)
        Client::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Path to the JSON file containing client data
        $jsonFile = storage_path('app/database/seeds/data/clients.json');

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

        // Insert each client into the database
        foreach ($jsonData as $clientData) {
            Client::create([
                'id' => $clientData['id'] ?? null,
                'client_name' => $clientData['client_name'] ?? null,
                'project_name' => $clientData['project_name'] ?? null,
                'poc' => $clientData['poc'] ?? null,
                'status' => $clientData['status'] ?? null,
                'quick_status' => $clientData['quick_status'] ?? null,
                'description' => $clientData['description'] ?? null,
                'corporate_name' => $clientData['corporate_name'] ?? null,
                'file1' => $clientData['file1'] ?? null,
                'file2' => $clientData['file2'] ?? null,
                'file3' => $clientData['file3'] ?? null,
                'project_services_total' => $clientData['project_services_total'] ?? null,
                'project_expenses_total' => $clientData['project_expenses_total'] ?? null,
                'final_services_total' => $clientData['final_services_total'] ?? null,
                'final_billing_total' => $clientData['final_billing_total'] ?? null,
                'created_at' => Carbon::parse($clientData['created_at'] ?? now()),
                'updated_at' => Carbon::parse($clientData['updated_at'] ?? now()),
            ]);
        }

        $this->command->info("Client data imported successfully from $jsonFile.");
    }
}
