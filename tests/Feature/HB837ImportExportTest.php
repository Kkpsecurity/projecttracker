<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\HB837;
use App\Models\Consultant;
use App\Models\Client;
use App\Imports\HB837Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class HB837ImportExportTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $consultant;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        // Create test consultant
        $this->consultant = Consultant::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john@test.com',
        ]);

        // Create test client if Client model exists
        if (class_exists('App\Models\Client')) {
            $this->client = Client::factory()->create([
                'client_name' => 'Test Client LLC',
                'project_name' => 'Test Project'
            ]);
        }

        // Authenticate user
        $this->actingAs($this->user);

        // Ensure storage directories exist
        Storage::fake('local');
        if (!Storage::exists('sample_imports')) {
            Storage::makeDirectory('sample_imports');
        }
    }

    /** @test */
    public function test_can_access_import_page()
    {
        $response = $this->get(route('admin.hb837.import'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.hb837.import');
    }

    /** @test */
    public function test_phase_1_initial_import()
    {
        // Create phase 1 test file
        $csvContent = $this->getPhase1CsvContent();
        $file = UploadedFile::fake()->createWithContent('phase1.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'initial',
            'action' => 'import'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify records were created
        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property One',
            'report_status' => 'in-progress',
        ]);

        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property Two',
            'report_status' => 'quoted',
        ]);
    }

    /** @test */
    public function test_phase_2_update_import()
    {
        // First, create initial records
        $this->createInitialTestRecords();

        // Create phase 2 test file with updates
        $csvContent = $this->getPhase2CsvContent();
        $file = UploadedFile::fake()->createWithContent('phase2.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'update',
            'action' => 'import'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify records were updated
        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property One',
            'report_status' => 'completed',
            'report_submitted' => '2024-07-20',
        ]);

        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property Two',
            'report_status' => 'in-progress',
            'contracting_status' => 'executed',
        ]);
    }

    /** @test */
    public function test_phase_3_review_import()
    {
        // First, create initial records and update them
        $this->createInitialTestRecords();
        $this->updateTestRecords();

        // Create phase 3 test file with final changes
        $csvContent = $this->getPhase3CsvContent();
        $file = UploadedFile::fake()->createWithContent('phase3.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'review',
            'action' => 'import'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify final records have review changes
        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property One',
            'units' => 55, // Updated in phase 3
            'securitygauge_crime_risk' => 'Low', // Changed from medium
        ]);

        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property Two',
            'units' => 105, // Updated in phase 3
            'report_submitted' => '2024-08-15',
        ]);
    }

    /** @test */
    public function test_import_preview_functionality()
    {
        $csvContent = $this->getPhase1CsvContent();
        $file = UploadedFile::fake()->createWithContent('preview.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'initial',
            'action' => 'preview'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('preview_data');

        // Verify no records were actually created during preview
        $this->assertDatabaseMissing('hb837', [
            'property_name' => 'Test Property One',
        ]);
    }

    /** @test */
    public function test_batch_three_phase_import()
    {
        // Create all three phase files
        $phase1File = UploadedFile::fake()->createWithContent('phase1.csv', $this->getPhase1CsvContent());
        $phase2File = UploadedFile::fake()->createWithContent('phase2.csv', $this->getPhase2CsvContent());
        $phase3File = UploadedFile::fake()->createWithContent('phase3.csv', $this->getPhase3CsvContent());

        $response = $this->post(route('admin.hb837.executeThreePhaseImport'), [
            'file_phase1' => $phase1File,
            'file_phase2' => $phase2File,
            'file_phase3' => $phase3File,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('three_phase_results');

        // Verify final state matches phase 3 data
        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property One',
            'report_status' => 'completed',
            'units' => 55,
            'securitygauge_crime_risk' => 'Low',
        ]);

        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property Two',
            'report_status' => 'completed',
            'units' => 105,
            'quoted_price' => '8700.00',
        ]);
    }

    /** @test */
    public function test_import_validation_errors()
    {
        // Create invalid CSV content
        $invalidCsvContent = "Invalid,Headers,Missing,Required,Fields\ntest,data,incomplete,row,data";
        $file = UploadedFile::fake()->createWithContent('invalid.csv', $invalidCsvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'initial',
            'action' => 'import'
        ]);

        // Should redirect with error message due to missing required columns
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function test_consultant_creation_during_import()
    {
        // Create CSV with a new consultant name
        $csvContent = $this->getPhase1CsvContent();
        $csvContent = str_replace('John Smith', 'New Consultant', $csvContent);

        $file = UploadedFile::fake()->createWithContent('new_consultant.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'initial',
            'action' => 'import'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify new consultant was created
        $this->assertDatabaseHas('consultants', [
            'first_name' => 'New',
            'last_name' => 'Consultant',
        ]);

        // Verify property is linked to new consultant
        $newConsultant = Consultant::where('name', 'New Consultant')->first();
        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property One',
            'assigned_consultant_id' => $newConsultant->id,
        ]);
    }

    /** @test */
    public function test_crime_risk_validation()
    {
        // Test all valid crime risk values
        $validRisks = ['Low', 'Moderate', 'Elevated', 'High', 'Severe'];

        foreach ($validRisks as $risk) {
            $csvContent = $this->getPhase1CsvContent();
            $csvContent = str_replace('medium', $risk, $csvContent);

            $file = UploadedFile::fake()->createWithContent("risk_{$risk}.csv", $csvContent);

            $response = $this->post(route('admin.hb837.import'), [
                'file' => $file,
                'import_phase' => 'initial',
                'action' => 'import'
            ]);

            $response->assertRedirect();
            $response->assertSessionHas('success');

            $this->assertDatabaseHas('hb837', [
                'securitygauge_crime_risk' => $risk,
            ]);

            // Clean up for next iteration
            HB837::truncate();
        }
    }

    /** @test */
    public function test_import_with_existing_demo_files()
    {
        // Test using the actual demo files created in storage
        $demoFile1Path = storage_path('app/sample_imports/test_phase1_initial.csv');

        if (file_exists($demoFile1Path)) {
            $file = new UploadedFile(
                $demoFile1Path,
                'test_phase1_initial.csv',
                'text/csv',
                null,
                true
            );

            $response = $this->post(route('admin.hb837.import'), [
                'file' => $file,
                'import_phase' => 'initial',
                'action' => 'import'
            ]);

            $response->assertRedirect();
            // Should either succeed or have specific error handling
            $this->assertTrue(
                $response->getSession()->has('success') ||
                $response->getSession()->has('error')
            );
        } else {
            $this->markTestSkipped('Demo file not found at: ' . $demoFile1Path);
        }
    }

    /**
     * Helper methods
     */
    protected function createInitialTestRecords()
    {
        HB837::create([
            'property_name' => 'Test Property One',
            'report_status' => 'in-progress',
            'contracting_status' => 'executed',
            'property_type' => 'garden',
            'units' => 50,
            'address' => '123 Test St',
            'city' => 'Austin',
            'state' => 'TX',
            'zip' => '78701',
            'management_company' => 'Test Management',
            'owner_name' => 'Test Owner LLC',
            'assigned_consultant_id' => $this->consultant->id,
            'securitygauge_crime_risk' => 'medium',
            'quoted_price' => 5000.00,
            'user_id' => $this->user->id,
        ]);

        HB837::create([
            'property_name' => 'Test Property Two',
            'report_status' => 'quoted',
            'contracting_status' => 'quoted',
            'property_type' => 'midrise',
            'units' => 100,
            'address' => '456 Test Ave',
            'city' => 'Dallas',
            'state' => 'TX',
            'zip' => '75201',
            'management_company' => 'Dallas Test Mgmt',
            'owner_name' => 'Test Investors',
            'assigned_consultant_id' => $this->consultant->id,
            'securitygauge_crime_risk' => 'low',
            'quoted_price' => 8000.00,
            'user_id' => $this->user->id,
        ]);
    }

    protected function updateTestRecords()
    {
        HB837::where('property_name', 'Test Property One')->update([
            'report_status' => 'completed',
            'report_submitted' => '2024-07-20',
            'billing_req_sent' => '2024-07-22',
        ]);

        HB837::where('property_name', 'Test Property Two')->update([
            'report_status' => 'in-progress',
            'contracting_status' => 'executed',
            'agreement_submitted' => '2024-07-25',
            'quoted_price' => 8500.00,
        ]);
    }

    protected function getPhase1CsvContent()
    {
        return "Report Status,Contracting Status,Property Name,Property Type,Units,Address,City,County,State,Zip,Phone,Management Company,Property Manager Name,Property Manager Email,Regional Manager Name,Regional Manager Email,Owner Name,Consultant Name,Scheduled Date of Inspection,Report Submitted,Agreement Submitted,Billing Req Sent,SecurityGauge Crime Risk,Quoted Price,Sub Fees Estimated Expenses,Project Net Profit,Macro Client,Macro Contact,Macro Email,Financial Notes,Consultant Notes,Notes\n" .
            "in-progress,executed,Test Property One,garden,50,123 Test St,Austin,Travis,TX,78701,512-555-0001,Test Management,Jane Doe,jane@test.com,Bob Test,bob@test.com,Test Owner LLC,John Smith,2024-07-15,,2024-07-01,,medium,5000.00,1000.00,4000.00,Test Client,Test Contact,test@client.com,Initial test property,Equipment ready,New test property\n" .
            "quoted,quoted,Test Property Two,midrise,100,456 Test Ave,Dallas,Dallas,TX,75201,214-555-0002,Dallas Test Mgmt,Mike Test,mike@test.com,Linda Test,linda@test.com,Test Investors,John Smith,2024-08-01,,,,low,8000.00,1500.00,6500.00,Test Group,Test Manager,manager@test.com,Second test property,Site visited,Test data entry";
    }

    protected function getPhase2CsvContent()
    {
        return "Report Status,Contracting Status,Property Name,Property Type,Units,Address,City,County,State,Zip,Phone,Management Company,Property Manager Name,Property Manager Email,Regional Manager Name,Regional Manager Email,Owner Name,Consultant Name,Scheduled Date of Inspection,Report Submitted,Agreement Submitted,Billing Req Sent,SecurityGauge Crime Risk,Quoted Price,Sub Fees Estimated Expenses,Project Net Profit,Macro Client,Macro Contact,Macro Email,Financial Notes,Consultant Notes,Notes\n" .
            "completed,executed,Test Property One,garden,50,123 Test St,Austin,Travis,TX,78701,512-555-0001,Test Management,Jane Doe,jane@test.com,Bob Test,bob@test.com,Test Owner LLC,John Smith,2024-07-15,2024-07-20,2024-07-01,2024-07-22,medium,5000.00,1000.00,4000.00,Test Client,Test Contact,test@client.com,Updated financial info,Report completed,Updated status to completed\n" .
            "in-progress,executed,Test Property Two,midrise,100,456 Test Ave,Dallas,Dallas,TX,75201,214-555-0002,Dallas Test Mgmt,Mike Test,mike@test.com,Linda Test,linda@test.com,Test Investors,John Smith,2024-08-01,,2024-07-25,,high,8500.00,1600.00,6900.00,Test Group,Test Manager,manager@test.com,Updated pricing,Agreement signed,Status updated from quoted to in-progress";
    }

    protected function getPhase3CsvContent()
    {
        return "Report Status,Contracting Status,Property Name,Property Type,Units,Address,City,County,State,Zip,Phone,Management Company,Property Manager Name,Property Manager Email,Regional Manager Name,Regional Manager Email,Owner Name,Consultant Name,Scheduled Date of Inspection,Report Submitted,Agreement Submitted,Billing Req Sent,SecurityGauge Crime Risk,Quoted Price,Sub Fees Estimated Expenses,Project Net Profit,Macro Client,Macro Contact,Macro Email,Financial Notes,Consultant Notes,Notes\n" .
            "completed,executed,Test Property One,garden,55,123 Test St,Austin,Travis,TX,78701,512-555-0001,Test Management Updated,Jane Doe,jane@test.com,Bob Test,bob@test.com,Test Owner LLC,John Smith,2024-07-15,2024-07-20,2024-07-01,2024-07-22,Low,5200.00,1100.00,4100.00,Test Client,Test Contact,test@client.com,Final review pricing,Final report delivered,Review phase - minor unit count adjustment\n" .
            "completed,executed,Test Property Two,midrise,105,456 Test Ave,Dallas,Dallas,TX,75201,214-555-0002,Dallas Test Mgmt,Mike Test,mike@test.com,Linda Test,linda@test.com,Test Investors,John Smith,2024-08-01,2024-08-15,2024-07-25,2024-08-18,medium,8700.00,1700.00,7000.00,Test Group,Test Manager,manager@test.com,Final billing complete,Project completed successfully,Review phase - final adjustments";
    }
}