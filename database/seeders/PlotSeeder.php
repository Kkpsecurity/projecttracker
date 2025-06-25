<?php

namespace Database\Seeders;

use App\Models\Plot;
use App\Models\PlotAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the tables
        PlotAddress::truncate();
        Plot::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $plots = [
            [
                'plot_name' => 'Downtown Commercial District',
                'addresses' => [
                    [
                        'latitude' => 33.7490,
                        'longitude' => -84.3880,
                        'location_name' => 'Main Plaza Center',
                    ],
                    [
                        'latitude' => 33.7495,
                        'longitude' => -84.3875,
                        'location_name' => 'Secondary Building A',
                    ],
                ]
            ],
            [
                'plot_name' => 'Residential Park Vista',
                'addresses' => [
                    [
                        'latitude' => 34.0522,
                        'longitude' => -118.2437,
                        'location_name' => 'Vista Apartments Building 1',
                    ],
                    [
                        'latitude' => 34.0520,
                        'longitude' => -118.2440,
                        'location_name' => 'Vista Apartments Building 2',
                    ],
                    [
                        'latitude' => 34.0518,
                        'longitude' => -118.2435,
                        'location_name' => 'Community Center',
                    ],
                ]
            ],
            [
                'plot_name' => 'Industrial Complex West',
                'addresses' => [
                    [
                        'latitude' => 32.7767,
                        'longitude' => -96.7970,
                        'location_name' => 'Warehouse Facility 1',
                    ],
                    [
                        'latitude' => 32.7770,
                        'longitude' => -96.7975,
                        'location_name' => 'Manufacturing Plant',
                    ],
                ]
            ],
            [
                'plot_name' => 'University Campus North',
                'addresses' => [
                    [
                        'latitude' => 30.2849,
                        'longitude' => -97.7341,
                        'location_name' => 'Student Dormitory A',
                    ],
                    [
                        'latitude' => 30.2852,
                        'longitude' => -97.7338,
                        'location_name' => 'Student Dormitory B',
                    ],
                    [
                        'latitude' => 30.2847,
                        'longitude' => -97.7344,
                        'location_name' => 'Academic Building',
                    ],
                    [
                        'latitude' => 30.2854,
                        'longitude' => -97.7340,
                        'location_name' => 'Recreation Center',
                    ],
                ]
            ],
            [
                'plot_name' => 'Shopping Center East',
                'addresses' => [
                    [
                        'latitude' => 33.4484,
                        'longitude' => -112.0740,
                        'location_name' => 'Main Retail Building',
                    ],
                    [
                        'latitude' => 33.4486,
                        'longitude' => -112.0738,
                        'location_name' => 'Anchor Store',
                    ],
                    [
                        'latitude' => 33.4482,
                        'longitude' => -112.0742,
                        'location_name' => 'Food Court',
                    ],
                ]
            ],
            [
                'plot_name' => 'Office Park Central',
                'addresses' => [
                    [
                        'latitude' => 39.7392,
                        'longitude' => -104.9903,
                        'location_name' => 'Corporate Tower A',
                    ],
                    [
                        'latitude' => 39.7395,
                        'longitude' => -104.9900,
                        'location_name' => 'Corporate Tower B',
                    ],
                ]
            ],
            [
                'plot_name' => 'Medical Complex',
                'addresses' => [
                    [
                        'latitude' => 25.7617,
                        'longitude' => -80.1918,
                        'location_name' => 'Main Hospital Building',
                    ],
                    [
                        'latitude' => 25.7620,
                        'longitude' => -80.1915,
                        'location_name' => 'Outpatient Clinic',
                    ],
                    [
                        'latitude' => 25.7615,
                        'longitude' => -80.1920,
                        'location_name' => 'Emergency Department',
                    ],
                ]
            ],
            [
                'plot_name' => 'Technology Park',
                'addresses' => [
                    [
                        'latitude' => 40.7589,
                        'longitude' => -111.8883,
                        'location_name' => 'Tech Hub Building 1',
                    ],
                    [
                        'latitude' => 40.7592,
                        'longitude' => -111.8880,
                        'location_name' => 'Tech Hub Building 2',
                    ],
                    [
                        'latitude' => 40.7587,
                        'longitude' => -111.8885,
                        'location_name' => 'Innovation Center',
                    ],
                ]
            ],
        ];

        foreach ($plots as $plotData) {
            // Create the plot
            $plot = Plot::create([
                'plot_name' => $plotData['plot_name'],
                'created_at' => Carbon::now()->subDays(rand(30, 120)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            // Create the associated addresses
            foreach ($plotData['addresses'] as $addressData) {
                PlotAddress::create([
                    'plot_id' => $plot->id,
                    'latitude' => $addressData['latitude'],
                    'longitude' => $addressData['longitude'],
                    'location_name' => $addressData['location_name'],
                    'created_at' => Carbon::now()->subDays(rand(30, 120)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $totalAddresses = PlotAddress::count();
        $this->command->info('Plot data seeded successfully - ' . count($plots) . ' plots and ' . $totalAddresses . ' addresses created.');
    }
}
