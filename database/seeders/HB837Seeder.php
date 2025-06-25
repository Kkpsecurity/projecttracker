<?php

namespace Database\Seeders;

use App\Models\HB837;
use App\Models\User;
use App\Models\Consultant;
use App\Models\Owner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HB837Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the table
        HB837::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get some users and consultants for relationships
        $users = User::all();
        $consultants = Consultant::all();
        $owners = Owner::all();

        if ($users->isEmpty()) {
            $this->command->error("Please run UserSeeder first - no users found");
            return;
        }

        if ($consultants->isEmpty()) {
            $this->command->error("Please run ConsultantSeeder first - no consultants found");
            return;
        }

        if ($owners->isEmpty()) {
            $this->command->error("Please run OwnerSeeder first - no owners found");
            return;
        }

        $hb837Records = [
            [
                'report_status' => 'not-started',
                'property_name' => 'Sunset Apartments',
                'management_company' => 'Premier Property Management',
                'owner_id' => $owners->random()->id,
                'owner_name' => 'John Doe Properties LLC',
                'property_type' => 'garden',
                'units' => 150,
                'address' => '1234 Sunset Blvd',
                'city' => 'Los Angeles',
                'county' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90210',
                'phone' => '(555) 123-4567',
                'assigned_consultant_id' => $consultants->random()->id,
                'scheduled_date_of_inspection' => Carbon::now()->addDays(30),
                'report_submitted' => null,
                'billing_req_sent' => null,
                'financial_notes' => 'Standard inspection rate applies',
                'securitygauge_crime_risk' => 'Moderate',
                'notes' => 'Initial inspection scheduled for residential complex',
                'property_manager_name' => 'Jane Smith',
                'property_manager_email' => 'jane.smith@premierpm.com',
                'regional_manager_name' => 'Bob Johnson',
                'regional_manager_email' => 'bob.johnson@premierpm.com',
                'agreement_submitted' => Carbon::now()->subDays(5),
                'contracting_status' => 'executed',
                'quoted_price' => 8500.00,
                'sub_fees_estimated_expenses' => 1200.00,
                'project_net_profit' => 7300.00,
                'macro_client' => 'Premier Property Management',
                'macro_contact' => 'Jane Smith',
                'macro_email' => 'jane.smith@premierpm.com',
                'user_id' => $users->random()->id,
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'report_status' => 'completed',
                'property_name' => 'Oak Grove Shopping Center',
                'management_company' => 'Retail Space Solutions',
                'owner_id' => $owners->random()->id,
                'owner_name' => 'Commercial Investments Inc',
                'property_type' => 'industrial',
                'units' => 25,
                'address' => '5678 Oak Grove Ave',
                'city' => 'Dallas',
                'county' => 'Dallas',
                'state' => 'TX',
                'zip' => '75201',
                'phone' => '(555) 987-6543',
                'assigned_consultant_id' => $consultants->random()->id,
                'scheduled_date_of_inspection' => Carbon::now()->subDays(45),
                'report_submitted' => Carbon::now()->subDays(15),
                'billing_req_sent' => Carbon::now()->subDays(10),
                'financial_notes' => 'Payment received in full',
                'securitygauge_crime_risk' => 'High',
                'notes' => 'Comprehensive security assessment completed',
                'property_manager_name' => 'Mike Wilson',
                'property_manager_email' => 'mike.wilson@retailspace.com',
                'regional_manager_name' => 'Sarah Davis',
                'regional_manager_email' => 'sarah.davis@retailspace.com',
                'agreement_submitted' => Carbon::now()->subDays(50),
                'contracting_status' => 'closed',
                'quoted_price' => 12500.00,
                'sub_fees_estimated_expenses' => 2000.00,
                'project_net_profit' => 10500.00,
                'macro_client' => 'Retail Space Solutions',
                'macro_contact' => 'Mike Wilson',
                'macro_email' => 'mike.wilson@retailspace.com',
                'user_id' => $users->random()->id,
                'created_at' => Carbon::now()->subDays(60),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'report_status' => 'in-progress',
                'property_name' => 'Riverside Industrial Park',
                'management_company' => 'Industrial Management Corp',
                'owner_id' => $owners->random()->id,
                'owner_name' => 'Riverside Holdings',
                'property_type' => 'industrial',
                'units' => 8,
                'address' => '9012 Riverside Dr',
                'city' => 'Phoenix',
                'county' => 'Maricopa',
                'state' => 'AZ',
                'zip' => '85001',
                'phone' => '(555) 456-7890',
                'assigned_consultant_id' => $consultants->random()->id,
                'scheduled_date_of_inspection' => Carbon::now()->subDays(10),
                'report_submitted' => null,
                'billing_req_sent' => null,
                'financial_notes' => 'Large project - milestone billing approved',
                'securitygauge_crime_risk' => 'Low',
                'notes' => 'Multi-phase assessment in progress',
                'property_manager_name' => 'Tom Rodriguez',
                'property_manager_email' => 'tom.rodriguez@industrialmgmt.com',
                'regional_manager_name' => 'Lisa Chen',
                'regional_manager_email' => 'lisa.chen@industrialmgmt.com',
                'agreement_submitted' => Carbon::now()->subDays(20),
                'contracting_status' => 'executed',
                'quoted_price' => 15000.00,
                'sub_fees_estimated_expenses' => 3000.00,
                'project_net_profit' => 12000.00,
                'macro_client' => 'Industrial Management Corp',
                'macro_contact' => 'Tom Rodriguez',
                'macro_email' => 'tom.rodriguez@industrialmgmt.com',
                'user_id' => $users->random()->id,
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'report_status' => 'not-started',
                'property_name' => 'University Heights Dormitory',
                'management_company' => 'Campus Living Solutions',
                'owner_id' => $owners->random()->id,
                'owner_name' => 'State University Foundation',
                'property_type' => 'midrise',
                'units' => 300,
                'address' => '1111 University Way',
                'city' => 'Austin',
                'county' => 'Travis',
                'state' => 'TX',
                'zip' => '78712',
                'phone' => '(555) 111-2222',
                'assigned_consultant_id' => $consultants->random()->id,
                'scheduled_date_of_inspection' => Carbon::now()->addDays(45),
                'report_submitted' => null,
                'billing_req_sent' => null,
                'financial_notes' => 'Educational discount rate applied',
                'securitygauge_crime_risk' => 'Moderate',
                'notes' => 'Campus security assessment required before semester',
                'property_manager_name' => 'Dr. Amanda Taylor',
                'property_manager_email' => 'amanda.taylor@campusliving.edu',
                'regional_manager_name' => 'Mark Thompson',
                'regional_manager_email' => 'mark.thompson@campusliving.edu',
                'agreement_submitted' => null,
                'contracting_status' => 'quoted',
                'quoted_price' => 9500.00,
                'sub_fees_estimated_expenses' => 1500.00,
                'project_net_profit' => 8000.00,
                'macro_client' => 'Campus Living Solutions',
                'macro_contact' => 'Dr. Amanda Taylor',
                'macro_email' => 'amanda.taylor@campusliving.edu',
                'user_id' => $users->random()->id,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'report_status' => 'completed',
                'property_name' => 'Downtown Office Plaza',
                'management_company' => 'Metro Commercial Properties',
                'owner_id' => $owners->random()->id,
                'owner_name' => 'Downtown Development LLC',
                'property_type' => 'highrise',
                'units' => 45,
                'address' => '2222 Main Street',
                'city' => 'Denver',
                'county' => 'Denver',
                'state' => 'CO',
                'zip' => '80202',
                'phone' => '(555) 333-4444',
                'assigned_consultant_id' => $consultants->random()->id,
                'scheduled_date_of_inspection' => Carbon::now()->subDays(90),
                'report_submitted' => Carbon::now()->subDays(30),
                'billing_req_sent' => Carbon::now()->subDays(25),
                'financial_notes' => 'Awaiting final payment',
                'securitygauge_crime_risk' => 'Low',
                'notes' => 'Standard office building assessment completed',
                'property_manager_name' => 'Jennifer Lee',
                'property_manager_email' => 'jennifer.lee@metrocommercial.com',
                'regional_manager_name' => 'David Park',
                'regional_manager_email' => 'david.park@metrocommercial.com',
                'agreement_submitted' => Carbon::now()->subDays(95),
                'contracting_status' => 'closed',
                'quoted_price' => 7500.00,
                'sub_fees_estimated_expenses' => 800.00,
                'project_net_profit' => 6700.00,
                'macro_client' => 'Metro Commercial Properties',
                'macro_contact' => 'Jennifer Lee',
                'macro_email' => 'jennifer.lee@metrocommercial.com',
                'user_id' => $users->random()->id,
                'created_at' => Carbon::now()->subDays(120),
                'updated_at' => Carbon::now()->subDays(30),
            ],
        ];

        foreach ($hb837Records as $record) {
            HB837::create($record);
        }

        $this->command->info('HB837 data seeded successfully - ' . count($hb837Records) . ' records created.');
    }
}
