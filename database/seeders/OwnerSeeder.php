<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the table
        Owner::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $owners = [
            [
                'name' => 'John Doe Properties LLC',
                'email' => 'contact@johndoeproperties.com',
                'phone' => '(555) 123-4567',
                'address' => '1234 Business Blvd',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90210',
                'company_name' => 'John Doe Properties LLC',
                'tax_id' => '12-3456789',
                'created_at' => Carbon::now()->subDays(100),
                'updated_at' => Carbon::now()->subDays(50),
            ],
            [
                'name' => 'Commercial Investments Inc',
                'email' => 'info@commercialinvestments.com',
                'phone' => '(555) 987-6543',
                'address' => '5678 Investment Ave',
                'city' => 'Dallas',
                'state' => 'TX',
                'zip' => '75201',
                'company_name' => 'Commercial Investments Inc',
                'tax_id' => '98-7654321',
                'created_at' => Carbon::now()->subDays(150),
                'updated_at' => Carbon::now()->subDays(75),
            ],
            [
                'name' => 'Riverside Holdings',
                'email' => 'management@riversideholdings.com',
                'phone' => '(555) 456-7890',
                'address' => '9012 Riverside Plaza',
                'city' => 'Phoenix',
                'state' => 'AZ',
                'zip' => '85001',
                'company_name' => 'Riverside Holdings',
                'tax_id' => '45-6789012',
                'created_at' => Carbon::now()->subDays(80),
                'updated_at' => Carbon::now()->subDays(40),
            ],
            [
                'name' => 'State University Foundation',
                'email' => 'foundation@stateuniversity.edu',
                'phone' => '(555) 111-2222',
                'address' => '1111 Campus Drive',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78712',
                'company_name' => 'State University Foundation',
                'tax_id' => '11-1222333',
                'created_at' => Carbon::now()->subDays(200),
                'updated_at' => Carbon::now()->subDays(100),
            ],
            [
                'name' => 'Downtown Development LLC',
                'email' => 'office@downtowndev.com',
                'phone' => '(555) 333-4444',
                'address' => '2222 Development Way',
                'city' => 'Denver',
                'state' => 'CO',
                'zip' => '80202',
                'company_name' => 'Downtown Development LLC',
                'tax_id' => '33-4445556',
                'created_at' => Carbon::now()->subDays(120),
                'updated_at' => Carbon::now()->subDays(60),
            ],
            [
                'name' => 'Suburban Properties Group',
                'email' => 'contact@suburbanproperties.com',
                'phone' => '(555) 555-6666',
                'address' => '3333 Suburban Lane',
                'city' => 'Atlanta',
                'state' => 'GA',
                'zip' => '30309',
                'company_name' => 'Suburban Properties Group',
                'tax_id' => '55-6667778',
                'created_at' => Carbon::now()->subDays(90),
                'updated_at' => Carbon::now()->subDays(45),
            ],
            [
                'name' => 'Coastal Real Estate Partners',
                'email' => 'info@coastalrep.com',
                'phone' => '(555) 777-8888',
                'address' => '4444 Ocean View Drive',
                'city' => 'Miami',
                'state' => 'FL',
                'zip' => '33101',
                'company_name' => 'Coastal Real Estate Partners',
                'tax_id' => '77-8889990',
                'created_at' => Carbon::now()->subDays(110),
                'updated_at' => Carbon::now()->subDays(55),
            ],
            [
                'name' => 'Mountain View Investments',
                'email' => 'contact@mountainviewinv.com',
                'phone' => '(555) 999-0000',
                'address' => '5555 Mountain Peak Road',
                'city' => 'Salt Lake City',
                'state' => 'UT',
                'zip' => '84101',
                'company_name' => 'Mountain View Investments',
                'tax_id' => '99-0001112',
                'created_at' => Carbon::now()->subDays(70),
                'updated_at' => Carbon::now()->subDays(35),
            ],
        ];

        foreach ($owners as $owner) {
            Owner::create($owner);
        }

        $this->command->info('Owner data seeded successfully - ' . count($owners) . ' records created.');
    }
}
