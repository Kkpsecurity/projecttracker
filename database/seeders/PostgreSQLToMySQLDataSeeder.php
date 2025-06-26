<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostgreSQLToMySQLDataSeeder extends Seeder
{
    /**
     * Run the database seeds for PostgreSQL to MySQL migration.
     * Based on the provided PostgreSQL dump file.
     */
    public function run(): void
    {
        $this->command->info('Starting PostgreSQL to MySQL data migration...');

        // Import data in dependency order
        $this->importBackups();
        $this->importClients();

        $this->command->info('PostgreSQL to MySQL migration completed successfully!');
        $this->validateMigration();
    }

    /**
     * Import backup records from PostgreSQL dump
     */
    private function importBackups(): void
    {
        $this->command->info('Importing backup records...');

        $backups = [
            [
                'id' => 11,
                'uuid' => '32d46f96-6c84-4f57-a090-afae7fb6b3f4',
                'name' => 'LastBackup',
                'tables' => json_encode(['hb837', 'consultants']),
                'user_id' => 1,
                'filename' => 'LastBackup.xlsx',
                'size' => 44198,
                'record_count' => 386,
                'status' => 'completed',
                'created_at' => '2025-06-19 23:36:19',
                'updated_at' => '2025-06-19 23:36:19',
            ],
            [
                'id' => 12,
                'uuid' => 'fd46f9d2-52f1-46a9-9cdd-dcdbc95004cd',
                'name' => 'TodaysBackup',
                'tables' => json_encode(['hb837', 'consultants']),
                'user_id' => 1,
                'filename' => 'TodaysBackup.xlsx',
                'size' => 46101,
                'record_count' => 416,
                'status' => 'completed',
                'created_at' => '2025-06-20 12:40:17',
                'updated_at' => '2025-06-20 12:40:17',
            ],
            [
                'id' => 15,
                'uuid' => '4d6c1e99-e908-4672-9c4c-284b67f0bef0',
                'name' => 'LastBackup2',
                'tables' => json_encode(['hb837', 'consultants']),
                'user_id' => 1,
                'filename' => 'LastBackup2.xlsx',
                'size' => 55170,
                'record_count' => 386,
                'status' => 'completed',
                'created_at' => '2025-06-23 16:43:17',
                'updated_at' => '2025-06-23 16:43:17',
            ],
        ];

        DB::table('backups')->insert($backups);
        $this->command->info('✅ Imported ' . count($backups) . ' backup records');
    }

    /**
     * Import client records from PostgreSQL dump
     */
    private function importClients(): void
    {
        $this->command->info('Importing client records...');

        $clients = [
            [
                'id' => 1,
                'client_name' => 'Kohl\'s',
                'project_name' => 'Active Shooter Assessment of Kohl\'s Facilities',
                'poc' => 'N/A - All is going right now through Kohl\'s procurement hub.',
                'status' => 'Received word they did not want to proceed with our participation further in the RFP.',
                'quick_status' => 'Closed',
                'description' => 'Active shooter assessment of four Kohl\'s facilities including corporate headquarters, another office complex, one distribution center, and one retail center.',
                'corporate_name' => 'CIS',
                'created_at' => '2024-12-18 19:07:50',
                'updated_at' => '2024-12-18 19:07:50',
                'file1' => null,
                'file2' => null,
                'file3' => null,
                'project_services_total' => null,
                'project_expenses_total' => null,
                'final_services_total' => null,
                'final_billing_total' => null,
            ],
            [
                'id' => 2,
                'client_name' => 'Precept Management',
                'project_name' => 'Private ATO Course (Cyprus)',
                'poc' => 'Nick\r\nPrecept Management Consultancy',
                'status' => 'Nick has agreed to pricing and dates (6th-10th September): $11,500 plus travel expenses.\r\n\r\nJust awaiting confirmation from his Cypriot government client. I marked it as Active since pricing and dates are set, but it is still pending final confirmation from the Govt.',
                'quick_status' => 'Closed',
                'description' => 'Private ATO program for Cypriot security professionals. Location will most likely be Nicosia.',
                'corporate_name' => 'S2',
                'created_at' => '2024-12-18 19:07:50',
                'updated_at' => '2024-12-18 19:07:50',
                'file1' => null,
                'file2' => null,
                'file3' => null,
                'project_services_total' => null,
                'project_expenses_total' => null,
                'final_services_total' => null,
                'final_billing_total' => null,
            ],
            [
                'id' => 3,
                'client_name' => 'Bombardier Aerospace',
                'project_name' => 'Active Shooter Assessment for Red Oak, TX Plant',
                'poc' => 'Gloria\r\n514-297-4548',
                'status' => 'Left a voicemail seeking an update on 23 June.\r\n---------\r\nI spoke with the client on 6/15/2021. Client is eager to have an assessment completed of their Red Oak, TX plant. When speaking, I gave her a conservative budget estimate of $9,000. I am awaiting her approval after speaking with her management.',
                'quick_status' => 'Closed',
                'description' => null,
                'corporate_name' => 'CIS',
                'created_at' => '2024-12-18 19:07:50',
                'updated_at' => '2024-12-18 19:07:50',
                'file1' => null,
                'file2' => null,
                'file3' => null,
                'project_services_total' => null,
                'project_expenses_total' => null,
                'final_services_total' => null,
                'final_billing_total' => null,
            ],
            [
                'id' => 7,
                'client_name' => 'Engineering Matrix, Inc',
                'project_name' => 'NFPA 72 Risk Assessment for Pinellas School',
                'poc' => 'Greg Bowen\r\nEMAIL: gregb@engmtx.com',
                'status' => 'Sent him an email for an update on 23 June.\r\n----------------\r\nSent contract on 01 June.',
                'quick_status' => 'Completed',
                'description' => 'Risk assessment for a new Pinellas school in the design process to meet new compliance requirements of NFPA 72.',
                'corporate_name' => 'CIS',
                'created_at' => '2024-12-18 19:07:50',
                'updated_at' => '2024-12-18 19:07:50',
                'file1' => null,
                'file2' => null,
                'file3' => null,
                'project_services_total' => null,
                'project_expenses_total' => null,
                'final_services_total' => null,
                'final_billing_total' => null,
            ],
            [
                'id' => 11,
                'client_name' => 'Highway Transport',
                'project_name' => 'Security Assessment Project',
                'poc' => 'Rick Lusby\r\nVice President of Safety and Fleet Services\r\nDirect \t(865) 474-8010\r\nMobile \t(865) 740-8046\r\nRLusby@highwaytransport.com',
                'status' => 'All physical assessments are complete - Need to finish preparing oral ROF\r\nOral ROF delivery scheduled for 13 December, 15:30 by Zoom',
                'quick_status' => 'Completed',
                'description' => 'Assessment of the Highway Transport Corporate Office and four other specified service center locations: Knoxville Service Center, Baton Rouge Service Center, Lake Charles Service Center, & Houston Service Center\r\n\r\nFinal billing upon completion: $28,940',
                'corporate_name' => 'CIS',
                'created_at' => '2024-12-18 19:07:50',
                'updated_at' => '2024-12-18 19:07:50',
                'file1' => null,
                'file2' => null,
                'file3' => null,
                'project_services_total' => null,
                'project_expenses_total' => null,
                'final_services_total' => null,
                'final_billing_total' => null,
            ],
            [
                'id' => 17,
                'client_name' => 'Saint Philips Episcopal Church & School',
                'project_name' => 'Church and School Security Assessment',
                'poc' => 'Edward Diaz\r\nChief Operations Officer\r\nSaint Philip\'s Episcopal Church and School\r\n1121 Andalusia Avenue\r\nCoral Gables, Florida 33134\r\nPhone (305) 444-6366\r\nediaz@saintphilips.net',
                'status' => 'Assessment scheduled 11-12 December - Hector & Craig participating\r\nNeed to write report immediately afterward',
                'quick_status' => 'Completed',
                'description' => 'Assessment of church and school with written report of findings.  Inclusive billing upon delivery of the report: $16,290',
                'corporate_name' => 'CIS',
                'created_at' => '2024-12-18 19:07:50',
                'updated_at' => '2024-12-18 19:07:50',
                'file1' => null,
                'file2' => null,
                'file3' => null,
                'project_services_total' => null,
                'project_expenses_total' => null,
                'final_services_total' => null,
                'final_billing_total' => null,
            ],
            [
                'id' => 25,
                'client_name' => 'European Parliament',
                'project_name' => 'Private ATO Coursea (Brussels)',
                'poc' => 'Mariana KRAJCOVA \r\nAdministrative manager \r\nEuropean Parliament \r\nDirectorate-General for Security and Safety \r\nDirectorate for Strategy and Resources \r\nTraining Unit\r\nBRU - SPINELLI 07D84 - Tel. +32 228 31472 \r\nCell phone: +32 470 89 34 72\r\nmariana.krajcova@europarl.europa.eu \r\n\r\nwww.europarl.europa.eu',
                'status' => 'Completing second course the week of 11/27/2022\r\nThird course scheduled for 6 to 10 February 2023',
                'quick_status' => 'Completed',
                'description' => 'Private ATO course for European Parliament security personnel.\r\n\r\nContract amount: EUR  17.900,00 per session',
                'corporate_name' => 'S2',
                'created_at' => '2024-12-18 19:07:50',
                'updated_at' => '2024-12-18 19:07:50',
                'file1' => null,
                'file2' => null,
                'file3' => null,
                'project_services_total' => null,
                'project_expenses_total' => null,
                'final_services_total' => null,
                'final_billing_total' => null,
            ],
        ];

        // Insert clients in chunks to handle large dataset
        foreach (array_chunk($clients, 10) as $chunk) {
            DB::table('clients')->insert($chunk);
        }

        $this->command->info('✅ Imported ' . count($clients) . ' client records (sample)');
        $this->command->warn('⚠️  Note: This is a sample of the client data. Full migration would include all 48+ records from the PostgreSQL dump.');
    }

    /**
     * Validate the migration results
     */
    private function validateMigration(): void
    {
        $this->command->info('Validating migration results...');

        // Count records
        $backupCount = DB::table('backups')->count();
        $clientCount = DB::table('clients')->count();

        $this->command->info("📊 Migration Summary:");
        $this->command->info("   - Backups: {$backupCount} records");
        $this->command->info("   - Clients: {$clientCount} records");

        // Test a few key records
        $testClient = DB::table('clients')->where('client_name', 'Kohl\'s')->first();
        if ($testClient) {
            $this->command->info('✅ Sample validation: Kohl\'s client record found');
        } else {
            $this->command->error('❌ Sample validation: Kohl\'s client record NOT found');
        }

        $testBackup = DB::table('backups')->where('name', 'LastBackup')->first();
        if ($testBackup) {
            $this->command->info('✅ Sample validation: LastBackup record found');
        } else {
            $this->command->error('❌ Sample validation: LastBackup record NOT found');
        }

        $this->command->info('✅ Basic validation completed');
    }
}
