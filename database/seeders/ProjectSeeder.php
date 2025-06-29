<?php

namespace Database\Seeders;

use App\Models\HB837;
use App\Models\Plot;
use App\Models\PlotAddress;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the migrations.
     */
    public function run(): void
    {
        // Create test HB837 projects
        $projects = [
            [
                'assigned_consultant_id' => 1,
                'owner_name' => 'ABC Development Company',
                'property_name' => 'Sunset Village Apartments',
                'property_type' => 'midrise',
                'units' => 120,
                'management_company' => 'Premier Property Management',
                'address' => '123 Sunset Boulevard',
                'city' => 'Austin',
                'county' => 'Travis',
                'state' => 'TX',
                'zip' => '78701',
                'phone' => '512-555-0101',
                'report_status' => 'in-progress',
                'contracting_status' => 'started',
                'scheduled_date_of_inspection' => now()->addDays(7),
                'quoted_price' => 15000.00,
                'sub_fees_estimated_expenses' => 2500.00,
                'project_net_profit' => 12500.00,
                'notes' => 'Large residential complex requiring comprehensive lighting assessment',
            ],
            [
                'assigned_consultant_id' => 2,
                'owner_name' => 'XYZ Commercial Properties',
                'property_name' => 'Downtown Office Plaza',
                'property_type' => 'highrise',
                'units' => 50,
                'management_company' => 'Elite Commercial Management',
                'address' => '456 Commerce Street',
                'city' => 'Dallas',
                'county' => 'Dallas',
                'state' => 'TX',
                'zip' => '75201',
                'phone' => '214-555-0202',
                'report_status' => 'not-started',
                'contracting_status' => 'quoted',
                'scheduled_date_of_inspection' => now()->addDays(14),
                'quoted_price' => 25000.00,
                'sub_fees_estimated_expenses' => 4000.00,
                'project_net_profit' => 21000.00,
                'notes' => 'High-priority commercial project for major client',
            ],
            [
                'assigned_consultant_id' => 3,
                'owner_name' => 'Garden Homes LLC',
                'property_name' => 'Riverside Gardens',
                'property_type' => 'garden',
                'units' => 24,
                'management_company' => 'Riverside Property Services',
                'address' => '789 River Road',
                'city' => 'Houston',
                'county' => 'Harris',
                'state' => 'TX',
                'zip' => '77001',
                'phone' => '713-555-0303',
                'report_status' => 'completed',
                'contracting_status' => 'closed',
                'scheduled_date_of_inspection' => now()->subDays(30),
                'report_submitted' => now()->subDays(10),
                'billing_req_sent' => now()->subDays(8),
                'agreement_submitted' => now()->subDays(5),
                'quoted_price' => 8500.00,
                'sub_fees_estimated_expenses' => 1200.00,
                'project_net_profit' => 7300.00,
                'notes' => 'Successfully completed project - excellent client feedback',
            ],
        ];

        foreach ($projects as $projectData) {
            $project = HB837::create($projectData);

            // Create associated plot data
            $plot = Plot::create([
                'hb837_id' => $project->id,
                'lot_number' => rand(1, 100),
                'block_number' => rand(1, 20),
                'subdivision_name' => 'Test Subdivision ' . $project->id,
                'coordinates_latitude' => 30.2672 + (rand(-1000, 1000) / 10000),
                'coordinates_longitude' => -97.7431 + (rand(-1000, 1000) / 10000),
            ]);

            // Create associated address
            PlotAddress::create([
                'plot_id' => $plot->id,
                'street_address' => rand(100, 9999) . ' Test Street',
                'city' => $project->city,
                'state' => $project->state,
                'zip_code' => $project->zip,
            ]);
        }

        $this->command->info('Project test data seeded successfully!');
    }
}
