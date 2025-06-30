<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\HB837;
use App\Models\Consultant;
use App\Imports\HB837Import;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class HB837ThreePhaseImportTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $consultant;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'is_admin' => true,
            'role' => 'superadmin'
        ]);
        
        // Create test consultant
        $this->consultant = Consultant::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@example.com'
        ]);
        
        $this->actingAs($this->user);
    }

    public function test_can_access_import_page()
    {
        $response = $this->get(route('admin.hb837.import.show'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.hb837.import');
        $response->assertSee('Choose Import Method');
        $response->assertSee('Single Phase Import');
        $response->assertSee('Three Phase Batch Import');
    }

    public function test_phase1_initial_import_creates_new_records()
    {
        // Create CSV content for Phase 1
        $csvContent = "Property Name,Address,City,County,State,Zip,Property Type,Units,Owner Name,Consultant Name,Scheduled Date of Inspection,Quoted Price,Sub Fees Estimated Expenses\n";
        $csvContent .= "Test Property,123 Test St,Test City,Test County,CA,90210,garden,100,Test Owner,John Smith,2025-07-15,20000,4000\n";
        $csvContent .= "Another Property,456 Another St,Another City,Another County,CA,90211,mid-rise,200,Another Owner,John Smith,2025-07-20,30000,6000";

        $file = UploadedFile::fake()->createWithContent('test_phase1.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'initial',
            'action' => 'import'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verify records were created
        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property',
            'address' => '123 Test St',
            'quoted_price' => 20000,
            'sub_fees_estimated_expenses' => 4000,
            'project_net_profit' => 16000
        ]);
        
        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Another Property',
            'address' => '456 Another St',
            'quoted_price' => 30000,
            'sub_fees_estimated_expenses' => 6000,
            'project_net_profit' => 24000
        ]);
        
        $this->assertEquals(2, HB837::count());
    }

    public function test_phase2_update_existing_records()
    {
        // First create initial records
        $property1 = HB837::factory()->create([
            'property_name' => 'Test Property',
            'address' => '123 Test St',
            'contracting_status' => 'quoted',
            'report_status' => 'not-started'
        ]);

        $property2 = HB837::factory()->create([
            'property_name' => 'Another Property', 
            'address' => '456 Another St',
            'contracting_status' => 'quoted',
            'report_status' => 'not-started'
        ]);

        // Create CSV content for Phase 2 updates
        $csvContent = "Property Name,Address,Contracting Status,Report Status,Agreement Submitted,Property Manager Name,Property Manager Email,Notes\n";
        $csvContent .= "Test Property,123 Test St,executed,in-progress,2025-07-01,John Manager,john@test.com,Updated from phase 2\n";
        $csvContent .= "Another Property,456 Another St,executed,in-review,2025-07-05,Jane Manager,jane@test.com,Also updated from phase 2";

        $file = UploadedFile::fake()->createWithContent('test_phase2.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'update',
            'action' => 'import'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verify records were updated
        $property1->refresh();
        $property2->refresh();
        
        $this->assertEquals('executed', $property1->contracting_status);
        $this->assertEquals('in-progress', $property1->report_status);
        $this->assertEquals('John Manager', $property1->property_manager_name);
        $this->assertEquals('john@test.com', $property1->property_manager_email);
        
        $this->assertEquals('executed', $property2->contracting_status);
        $this->assertEquals('in-review', $property2->report_status);
        $this->assertEquals('Jane Manager', $property2->property_manager_name);
        $this->assertEquals('jane@test.com', $property2->property_manager_email);
    }

    public function test_phase3_review_updates_detailed_info()
    {
        // Create initial records
        $property1 = HB837::factory()->create([
            'property_name' => 'Test Property',
            'address' => '123 Test St',
            'quoted_price' => 20000,
            'sub_fees_estimated_expenses' => 4000,
            'securitygauge_crime_risk' => null
        ]);

        // Create CSV content for Phase 3 review
        $csvContent = "Property Name,Address,Quoted Price,Sub Fees Estimated Expenses,SecurityGauge Crime Risk,Macro Client,Macro Contact,Financial Notes\n";
        $csvContent .= "Test Property,123 Test St,22000,4500,Moderate,Test Insurance,Contact Person,Updated pricing after review";

        $file = UploadedFile::fake()->createWithContent('test_phase3.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'review',
            'action' => 'import'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verify record was updated with review phase data
        $property1->refresh();
        
        $this->assertEquals(22000, $property1->quoted_price);
        $this->assertEquals(4500, $property1->sub_fees_estimated_expenses);
        $this->assertEquals('Moderate', $property1->securitygauge_crime_risk);
        $this->assertEquals('Test Insurance', $property1->macro_client);
        $this->assertEquals('Contact Person', $property1->macro_contact);
        $this->assertEquals('Updated pricing after review', $property1->financial_notes);
    }

    public function test_preview_mode_shows_comparison_without_importing()
    {
        // Create existing record
        HB837::factory()->create([
            'property_name' => 'Test Property',
            'address' => '123 Test St',
            'quoted_price' => 20000,
            'report_status' => 'not-started'
        ]);

        $csvContent = "Property Name,Address,Report Status,Quoted Price\n";
        $csvContent .= "Test Property,123 Test St,in-progress,25000\n";
        $csvContent .= "New Property,456 New St,not-started,15000";

        $file = UploadedFile::fake()->createWithContent('test_preview.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'update',
            'action' => 'preview'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('preview_data');
        
        $previewData = session('preview_data');
        $this->assertEquals(2, $previewData['total_uploaded']);
        $this->assertEquals(1, $previewData['updated_count']);
        $this->assertEquals(0, $previewData['new_count']); // Phase 2 doesn't create new records
        
        // Verify no actual changes were made
        $property = HB837::where('property_name', 'Test Property')->first();
        $this->assertEquals('not-started', $property->report_status);
        $this->assertEquals(20000, $property->quoted_price);
    }

    public function test_three_phase_batch_import()
    {
        // Create test files for all three phases
        $phase1Content = "Property Name,Address,City,State,Quoted Price,Sub Fees Estimated Expenses\n";
        $phase1Content .= "Batch Property,789 Batch St,Batch City,CA,30000,6000";

        $phase2Content = "Property Name,Address,Contracting Status,Report Status\n";
        $phase2Content .= "Batch Property,789 Batch St,executed,in-progress";

        $phase3Content = "Property Name,Address,SecurityGauge Crime Risk,Macro Client\n";
        $phase3Content .= "Batch Property,789 Batch St,Low,Batch Insurance";

        $file1 = UploadedFile::fake()->createWithContent('batch_phase1.csv', $phase1Content);
        $file2 = UploadedFile::fake()->createWithContent('batch_phase2.csv', $phase2Content);
        $file3 = UploadedFile::fake()->createWithContent('batch_phase3.csv', $phase3Content);

        $response = $this->post(route('admin.hb837.three-phase-import'), [
            'file_phase1' => $file1,
            'file_phase2' => $file2,
            'file_phase3' => $file3
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('three_phase_results');
        
        $results = session('three_phase_results');
        $this->assertArrayHasKey('phase1', $results);
        $this->assertArrayHasKey('phase2', $results);
        $this->assertArrayHasKey('phase3', $results);
        $this->assertArrayHasKey('summary', $results);
        
        // Verify the property was created and updated through all phases
        $property = HB837::where('property_name', 'Batch Property')->first();
        $this->assertNotNull($property);
        $this->assertEquals('789 Batch St', $property->address);
        $this->assertEquals(30000, $property->quoted_price);
        $this->assertEquals('executed', $property->contracting_status);
        $this->assertEquals('in-progress', $property->report_status);
        $this->assertEquals('Low', $property->securitygauge_crime_risk);
        $this->assertEquals('Batch Insurance', $property->macro_client);
    }

    public function test_import_validation_errors()
    {
        // Test missing required fields
        $response = $this->post(route('admin.hb837.import'), [
            'import_phase' => 'initial',
            'action' => 'import'
            // Missing file
        ]);

        $response->assertSessionHasErrors(['file']);

        // Test invalid phase
        $file = UploadedFile::fake()->create('test.csv');
        
        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'invalid_phase',
            'action' => 'import'
        ]);

        $response->assertSessionHasErrors(['import_phase']);

        // Test invalid action
        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'initial',
            'action' => 'invalid_action'
        ]);

        $response->assertSessionHasErrors(['action']);
    }

    public function test_import_handles_consultant_creation()
    {
        $csvContent = "Property Name,Address,Consultant Name,Quoted Price\n";
        $csvContent .= "Test Property,123 Test St,New Consultant,20000";

        $file = UploadedFile::fake()->createWithContent('test_consultant.csv', $csvContent);

        $initialConsultantCount = Consultant::count();

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'initial',
            'action' => 'import'
        ]);

        $response->assertRedirect();
        
        // Verify new consultant was created
        $this->assertEquals($initialConsultantCount + 1, Consultant::count());
        
        $newConsultant = Consultant::where('first_name', 'New')->where('last_name', 'Consultant')->first();
        $this->assertNotNull($newConsultant);
        
        // Verify property was assigned to new consultant
        $property = HB837::where('property_name', 'Test Property')->first();
        $this->assertEquals($newConsultant->id, $property->assigned_consultant_id);
    }

    public function test_import_skips_records_with_missing_address()
    {
        $csvContent = "Property Name,Address,Quoted Price\n";
        $csvContent .= "Valid Property,123 Valid St,20000\n";
        $csvContent .= "Invalid Property,,15000"; // Missing address

        $file = UploadedFile::fake()->createWithContent('test_validation.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'initial',
            'action' => 'import'
        ]);

        $response->assertRedirect();
        
        // Verify only valid property was created
        $this->assertEquals(1, HB837::count());
        $this->assertDatabaseHas('hb837', ['property_name' => 'Valid Property']);
        $this->assertDatabaseMissing('hb837', ['property_name' => 'Invalid Property']);
    }

    public function test_crime_risk_validation_and_defaults()
    {
        $csvContent = "Property Name,Address,SecurityGauge Crime Risk\n";
        $csvContent .= "Property1,123 Test St,Low\n";
        $csvContent .= "Property2,456 Test St,Invalid Risk\n";
        $csvContent .= "Property3,789 Test St,"; // Empty risk

        $file = UploadedFile::fake()->createWithContent('test_risk.csv', $csvContent);

        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'review',
            'action' => 'import'
        ]);

        $response->assertRedirect();
        
        // Create initial records first for review phase to work
        HB837::factory()->create(['property_name' => 'Property1', 'address' => '123 Test St']);
        HB837::factory()->create(['property_name' => 'Property2', 'address' => '456 Test St']);
        HB837::factory()->create(['property_name' => 'Property3', 'address' => '789 Test St']);

        // Test again with existing records
        $response = $this->post(route('admin.hb837.import'), [
            'file' => $file,
            'import_phase' => 'review',
            'action' => 'import'
        ]);

        $properties = HB837::all();
        
        // Valid risk should be preserved
        $this->assertEquals('Low', $properties->where('property_name', 'Property1')->first()->securitygauge_crime_risk);
        
        // Invalid risk should default to Moderate
        $this->assertEquals('Moderate', $properties->where('property_name', 'Property2')->first()->securitygauge_crime_risk);
        
        // Empty risk should default to Moderate
        $this->assertEquals('Moderate', $properties->where('property_name', 'Property3')->first()->securitygauge_crime_risk);
    }
}
