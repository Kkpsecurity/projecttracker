<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\HB837;
use App\Models\User;
use App\Models\Consultant;
use App\Imports\HB837Import;
use App\Exports\HB837Export;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class HB837ImportExportTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Consultant $consultant;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user and consultant
        $this->user = User::factory()->create();
        $this->consultant = Consultant::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
        
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_export_hb837_records_with_all_agent_required_fields()
    {
        // Create test HB837 records with agent-required fields
        $hb837 = HB837::create([
            'property_name' => 'Test Property',
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'TX',
            'zip' => '12345',
            'county' => 'Test County',
            'phone' => '555-1234',
            'property_type' => 'garden',
            'units' => 100,
            'management_company' => 'Test Management',
            'property_manager_name' => 'Jane Manager',
            'property_manager_email' => 'jane@example.com',
            'regional_manager_name' => 'Bob Regional',
            'regional_manager_email' => 'bob@example.com',
            'owner_name' => 'Test Owner',
            'assigned_consultant_id' => $this->consultant->id,
            'scheduled_date_of_inspection' => '2024-01-15',
            'report_status' => 'in-progress',
            'contracting_status' => 'executed',
            'securitygauge_crime_risk' => 'medium',
            'quoted_price' => 5000.00,
            'sub_fees_estimated_expenses' => 1000.00,
            'project_net_profit' => 4000.00,
            'macro_client' => 'Big Client',
            'macro_contact' => 'Big Contact',
            'macro_email' => 'big@client.com',
            'report_submitted' => '2024-01-20',
            'agreement_submitted' => '2024-01-10',
            'billing_req_sent' => '2024-01-25',
            'financial_notes' => 'Test financial notes',
            'consultant_notes' => 'Test consultant notes',
            'notes' => 'Test general notes',
            'user_id' => $this->user->id
        ]);

        // Test export
        Excel::fake();
        
        $export = new HB837Export();
        Excel::store($export, 'test_export.xlsx');

        Excel::assertStored('test_export.xlsx');
        
        // Verify export contains all required fields
        $collection = $export->collection();
        $this->assertGreaterThan(0, $collection->count());
        
        $headings = $export->headings();
        $requiredFields = [
            'Report Status',
            'Contracting Status', 
            'Property Name',
            'Address',
            'City',
            'State',
            'Zip',
            'Phone',
            'Property Type',
            'Units',
            'Management Company',
            'Property Manager Name',
            'Property Manager Email',
            'Consultant Name',
            'Scheduled Date of Inspection',
            'SecurityGauge Crime Risk',
            'Quoted Price',
            'Report Submitted'
        ];
        
        foreach ($requiredFields as $field) {
            $this->assertContains($field, $headings, "Missing required field: {$field}");
        }
    }

    /** @test */
    public function it_can_import_agent_excel_file_and_update_existing_records()
    {
        // Create existing HB837 record
        $existing = HB837::create([
            'property_name' => 'Existing Property',
            'address' => '456 Existing St',
            'city' => 'Existing City',
            'state' => 'CA',
            'zip' => '67890',
            'report_status' => 'not-started',
            'contracting_status' => 'quoted',
            'user_id' => $this->user->id
        ]);

        // Create test CSV content with updated data
        $csvContent = implode("\n", [
            'Report Status,Contracting Status,Property Name,Property Type,Units,Address,City,County,State,Zip,Phone,Management Company,Property Manager Name,Property Manager Email,Regional Manager Name,Regional Manager Email,Owner Name,Consultant Name,Scheduled Date of Inspection,Report Submitted,Agreement Submitted,Billing Req Sent,SecurityGauge Crime Risk,Quoted Price,Sub Fees Estimated Expenses,Project Net Profit,Macro Client,Macro Contact,Macro Email,Financial Notes,Consultant Notes,Notes',
            'in-progress,executed,Existing Property,garden,150,456 Existing St,Existing City,Test County,CA,67890,555-9999,Updated Management,Jane Updated,jane.updated@example.com,Bob Updated,bob.updated@example.com,Updated Owner,John Doe,2024-02-01,2024-02-05,2024-01-28,2024-02-10,high,7500.00,1500.00,6000.00,Big Client Updated,Big Contact Updated,big.updated@client.com,Updated financial notes,Updated consultant notes,Updated general notes'
        ]);

        // Create temporary file
        Storage::fake('local');
        Storage::put('test_import.csv', $csvContent);
        $file = UploadedFile::fake()->createWithContent('test_import.csv', $csvContent);

        // Import the file
        $import = new HB837Import();
        Excel::import($import, $file);

        // Verify the record was updated
        $updated = HB837::where('address', '456 Existing St')->first();
        
        $this->assertEquals('in-progress', $updated->report_status);
        $this->assertEquals('executed', $updated->contracting_status);
        $this->assertEquals('garden', $updated->property_type);
        $this->assertEquals(150, $updated->units);
        $this->assertEquals('Updated Management', $updated->management_company);
        $this->assertEquals('Jane Updated', $updated->property_manager_name);
        $this->assertEquals('high', $updated->securitygauge_crime_risk);
        $this->assertEquals(7500.00, $updated->quoted_price);
        
        // Verify counters
        $this->assertEquals(0, $import->importedCount); // No new records
        $this->assertEquals(1, $import->updatedCount);  // One updated record
        $this->assertEquals(0, $import->skippedCount);
    }

    /** @test */
    public function it_can_import_new_agent_records()
    {
        // Create test CSV content with new records
        $csvContent = implode("\n", [
            'Report Status,Contracting Status,Property Name,Property Type,Units,Address,City,County,State,Zip,Phone,Management Company,Property Manager Name,Property Manager Email,Regional Manager Name,Regional Manager Email,Owner Name,Consultant Name,Scheduled Date of Inspection,Report Submitted,Agreement Submitted,Billing Req Sent,SecurityGauge Crime Risk,Quoted Price,Sub Fees Estimated Expenses,Project Net Profit,Macro Client,Macro Contact,Macro Email,Financial Notes,Consultant Notes,Notes',
            'quoted,quoted,New Property 1,midrise,200,789 New St,New City,New County,NY,11111,555-1111,New Management,New Manager,new@example.com,New Regional,regional@example.com,New Owner,John Doe,2024-03-01,,,,medium,8000.00,2000.00,6000.00,New Client,New Contact,new@client.com,New financial notes,New consultant notes,New general notes',
            'active,executed,New Property 2,highrise,300,321 Another St,Another City,Another County,FL,22222,555-2222,Another Management,Another Manager,another@example.com,Another Regional,another.regional@example.com,Another Owner,John Doe,2024-03-15,2024-03-20,2024-03-10,2024-03-25,low,10000.00,2500.00,7500.00,Another Client,Another Contact,another@client.com,Another financial notes,Another consultant notes,Another general notes'
        ]);

        Storage::fake('local');
        Storage::put('test_import_new.csv', $csvContent);
        $file = UploadedFile::fake()->createWithContent('test_import_new.csv', $csvContent);

        // Import the file
        $import = new HB837Import();
        Excel::import($import, $file);

        // Verify new records were created
        $this->assertEquals(2, $import->importedCount);
        $this->assertEquals(0, $import->updatedCount);
        $this->assertEquals(0, $import->skippedCount);

        // Verify specific record details
        $newRecord1 = HB837::where('address', '789 New St')->first();
        $this->assertNotNull($newRecord1);
        $this->assertEquals('New Property 1', $newRecord1->property_name);
        $this->assertEquals('midrise', $newRecord1->property_type);
        $this->assertEquals(200, $newRecord1->units);
        $this->assertEquals('medium', $newRecord1->securitygauge_crime_risk);

        $newRecord2 = HB837::where('address', '321 Another St')->first();
        $this->assertNotNull($newRecord2);
        $this->assertEquals('New Property 2', $newRecord2->property_name);
        $this->assertEquals('highrise', $newRecord2->property_type);
        $this->assertEquals(300, $newRecord2->units);
        $this->assertEquals('low', $newRecord2->securitygauge_crime_risk);
    }

    /** @test */
    public function it_validates_required_fields_during_import()
    {
        // Create test CSV with missing address (should be skipped)
        $csvContent = implode("\n", [
            'Report Status,Contracting Status,Property Name,Property Type,Units,Address,City,County,State,Zip,Phone,Management Company,Property Manager Name,Property Manager Email,Regional Manager Name,Regional Manager Email,Owner Name,Consultant Name,Scheduled Date of Inspection,Report Submitted,Agreement Submitted,Billing Req Sent,SecurityGauge Crime Risk,Quoted Price,Sub Fees Estimated Expenses,Project Net Profit,Macro Client,Macro Contact,Macro Email,Financial Notes,Consultant Notes,Notes',
            'quoted,quoted,Valid Property,garden,100,123 Valid St,Valid City,Valid County,TX,12345,555-0000,Valid Management,Valid Manager,valid@example.com,Valid Regional,valid.regional@example.com,Valid Owner,John Doe,2024-04-01,,,,medium,5000.00,1000.00,4000.00,Valid Client,Valid Contact,valid@client.com,Valid financial notes,Valid consultant notes,Valid general notes',
            'quoted,quoted,Invalid Property,garden,100,,Invalid City,Invalid County,TX,54321,555-1111,Invalid Management,Invalid Manager,invalid@example.com,Invalid Regional,invalid.regional@example.com,Invalid Owner,John Doe,2024-04-01,,,,medium,5000.00,1000.00,4000.00,Invalid Client,Invalid Contact,invalid@client.com,Invalid financial notes,Invalid consultant notes,Invalid general notes'
        ]);

        Storage::fake('local');
        Storage::put('test_validation.csv', $csvContent);
        $file = UploadedFile::fake()->createWithContent('test_validation.csv', $csvContent);

        // Import the file
        $import = new HB837Import();
        Excel::import($import, $file);

        // Verify only valid record was imported
        $this->assertEquals(1, $import->importedCount);
        $this->assertEquals(0, $import->updatedCount);
        $this->assertEquals(1, $import->skippedCount);

        // Verify valid record exists
        $validRecord = HB837::where('address', '123 Valid St')->first();
        $this->assertNotNull($validRecord);

        // Verify invalid record doesn't exist
        $invalidRecord = HB837::where('property_name', 'Invalid Property')->first();
        $this->assertNull($invalidRecord);
    }

    /** @test */
    public function it_handles_consultant_name_mapping_correctly()
    {
        // Create additional consultant
        $consultant2 = Consultant::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith'
        ]);

        $csvContent = implode("\n", [
            'Report Status,Contracting Status,Property Name,Property Type,Units,Address,City,County,State,Zip,Phone,Management Company,Property Manager Name,Property Manager Email,Regional Manager Name,Regional Manager Email,Owner Name,Consultant Name,Scheduled Date of Inspection,Report Submitted,Agreement Submitted,Billing Req Sent,SecurityGauge Crime Risk,Quoted Price,Sub Fees Estimated Expenses,Project Net Profit,Macro Client,Macro Contact,Macro Email,Financial Notes,Consultant Notes,Notes',
            'quoted,quoted,Consultant Test 1,garden,100,111 Consultant St,Test City,Test County,TX,12345,555-0001,Test Management,Test Manager,test@example.com,Test Regional,test.regional@example.com,Test Owner,John Doe,2024-05-01,,,,medium,5000.00,1000.00,4000.00,Test Client,Test Contact,test@client.com,Test financial notes,Test consultant notes,Test general notes',
            'quoted,quoted,Consultant Test 2,garden,100,222 Consultant St,Test City,Test County,TX,12345,555-0002,Test Management,Test Manager,test@example.com,Test Regional,test.regional@example.com,Test Owner,Jane Smith,2024-05-01,,,,medium,5000.00,1000.00,4000.00,Test Client,Test Contact,test@client.com,Test financial notes,Test consultant notes,Test general notes'
        ]);

        Storage::fake('local');
        Storage::put('test_consultant.csv', $csvContent);
        $file = UploadedFile::fake()->createWithContent('test_consultant.csv', $csvContent);

        // Import the file
        $import = new HB837Import();
        Excel::import($import, $file);

        // Verify records were created with correct consultant assignments
        $record1 = HB837::where('address', '111 Consultant St')->first();
        $this->assertNotNull($record1);
        $this->assertEquals($this->consultant->id, $record1->assigned_consultant_id);

        $record2 = HB837::where('address', '222 Consultant St')->first();
        $this->assertNotNull($record2);
        $this->assertEquals($consultant2->id, $record2->assigned_consultant_id);
    }

    /** @test */
    public function it_maintains_field_consistency_between_import_and_export()
    {
        // Create a comprehensive HB837 record
        $hb837 = HB837::create([
            'property_name' => 'Consistency Test Property',
            'address' => '999 Consistency St',
            'city' => 'Consistency City',
            'state' => 'TX',
            'zip' => '99999',
            'county' => 'Consistency County',
            'phone' => '555-9999',
            'property_type' => 'garden',
            'units' => 500,
            'management_company' => 'Consistency Management',
            'property_manager_name' => 'Consistency Manager',
            'property_manager_email' => 'consistency@example.com',
            'regional_manager_name' => 'Consistency Regional',
            'regional_manager_email' => 'consistency.regional@example.com',
            'owner_name' => 'Consistency Owner',
            'assigned_consultant_id' => $this->consultant->id,
            'scheduled_date_of_inspection' => '2024-06-01',
            'report_status' => 'completed',
            'contracting_status' => 'executed',
            'securitygauge_crime_risk' => 'high',
            'quoted_price' => 15000.00,
            'sub_fees_estimated_expenses' => 3000.00,
            'project_net_profit' => 12000.00,
            'macro_client' => 'Consistency Client',
            'macro_contact' => 'Consistency Contact',
            'macro_email' => 'consistency@client.com',
            'report_submitted' => '2024-06-05',
            'agreement_submitted' => '2024-05-25',
            'billing_req_sent' => '2024-06-10',
            'financial_notes' => 'Consistency financial notes',
            'consultant_notes' => 'Consistency consultant notes',
            'notes' => 'Consistency general notes',
            'user_id' => $this->user->id
        ]);

        // Export the record
        $export = new HB837Export();
        $exported = $export->collection()->first();

        // Verify all key fields are present in export
        $this->assertNotNull($exported);
        
        // Import the exported data back
        $exportHeadings = $export->headings();
        $exportData = $exported;
        
        // Create CSV from export
        $csvData = array_combine($exportHeadings, $exportData);
        $csvContent = implode(',', $exportHeadings) . "\n";
        $csvContent .= implode(',', array_map(function($value) {
            return '"' . str_replace('"', '""', $value ?? '') . '"';
        }, $exportData));

        // Delete the original record to test import
        $hb837->delete();

        Storage::fake('local');
        Storage::put('consistency_test.csv', $csvContent);
        $file = UploadedFile::fake()->createWithContent('consistency_test.csv', $csvContent);

        // Import back
        $import = new HB837Import();
        Excel::import($import, $file);

        // Verify the record was recreated correctly
        $reimported = HB837::where('address', '999 Consistency St')->first();
        $this->assertNotNull($reimported);
        $this->assertEquals('Consistency Test Property', $reimported->property_name);
        $this->assertEquals('completed', $reimported->report_status);
        $this->assertEquals('high', $reimported->securitygauge_crime_risk);
        $this->assertEquals(15000.00, $reimported->quoted_price);
    }
}
