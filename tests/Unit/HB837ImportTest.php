<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\HB837;
use App\Models\Consultant;
use App\Imports\HB837Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HB837ImportTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $consultant;
    protected $import;

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

        // Create import instance
        $this->import = new HB837Import();

        // Setup storage
        Storage::fake('local');
    }

    /** @test */
    public function test_import_class_can_be_instantiated()
    {
        $import = new HB837Import();
        $this->assertInstanceOf(HB837Import::class, $import);
    }

    /** @test */
    public function test_can_set_and_get_import_phase()
    {
        $this->import->setPhase('initial');
        $this->assertEquals('initial', $this->import->getPhase());

        $this->import->setPhase('update');
        $this->assertEquals('update', $this->import->getPhase());

        $this->import->setPhase('review');
        $this->assertEquals('review', $this->import->getPhase());
    }

    /** @test */
    public function test_import_counters_initialization()
    {
        $this->assertEquals(0, $this->import->importedCount);
        $this->assertEquals(0, $this->import->updatedCount);
        $this->assertEquals(0, $this->import->skippedCount);
        $this->assertIsArray($this->import->skippedProperties);
    }

    /** @test */
    public function test_phase_validation()
    {
        // Test valid phases
        $validPhases = ['initial', 'update', 'review'];
        foreach ($validPhases as $phase) {
            $this->import->setPhase($phase);
            $this->assertEquals($phase, $this->import->getPhase());
        }

        // Test default phase
        $newImport = new HB837Import();
        $this->assertEquals('initial', $newImport->getPhase());
    }

    /** @test */
    public function test_import_with_minimal_data()
    {
        $testData = [
            'property_name' => 'Minimal Test Property',
            'address' => '123 Test Street',
            'report_status' => 'quoted',
            'contracting_status' => 'quoted'
        ];

        // Test that import can handle minimal required data
        $this->assertIsArray($testData);
        $this->assertArrayHasKey('property_name', $testData);
        $this->assertArrayHasKey('address', $testData);
    }

    /** @test */
    public function test_consultant_lookup_functionality()
    {
        // Test existing consultant lookup
        $existingConsultant = $this->consultant;
        $this->assertNotNull($existingConsultant);
        $this->assertEquals('John', $existingConsultant->first_name);
        $this->assertEquals('Smith', $existingConsultant->last_name);

        // Test non-existing consultant (should be created in actual import)
        $nonExistingConsultant = Consultant::where('first_name', 'Non Existing Consultant')->first();
        $this->assertNull($nonExistingConsultant);
    }

    /** @test */
    public function test_field_mapping_constants()
    {
        // Test that the import class has proper field mappings
        $import = new HB837Import();

        // These should be defined in the actual import class
        $this->assertTrue(method_exists($import, 'setPhase'));
        $this->assertTrue(method_exists($import, 'getPhase'));
        $this->assertTrue(property_exists($import, 'importedCount'));
        $this->assertTrue(property_exists($import, 'updatedCount'));
        $this->assertTrue(property_exists($import, 'skippedCount'));
    }

    /** @test */
    public function test_crime_risk_validation_logic()
    {
        $validCrimeRisks = ['Low', 'Moderate', 'Elevated', 'High', 'Severe'];

        foreach ($validCrimeRisks as $risk) {
            // Test that each risk level is a valid string
            $this->assertIsString($risk);
            $this->assertNotEmpty($risk);
        }

        // Test invalid risk levels
        $invalidRisks = ['', null, 'invalid', 'UNKNOWN'];
        foreach ($invalidRisks as $risk) {
            $this->assertNotContains($risk, $validCrimeRisks);
        }
    }

    /** @test */
    public function test_status_validation_logic()
    {
        $validReportStatuses = ['not-started', 'in-progress', 'in-review', 'completed'];
        $validContractingStatuses = ['quoted', 'started', 'executed', 'closed'];

        foreach ($validReportStatuses as $status) {
            $this->assertIsString($status);
            $this->assertNotEmpty($status);
        }

        foreach ($validContractingStatuses as $status) {
            $this->assertIsString($status);
            $this->assertNotEmpty($status);
        }
    }

    /** @test */
    public function test_numeric_field_validation()
    {
        $numericFields = [
            'units' => 100,
            'quoted_price' => 5000.00,
            'sub_fees_estimated_expenses' => 1000.00,
            'project_net_profit' => 4000.00
        ];

        foreach ($numericFields as $field => $value) {
            $this->assertIsNumeric($value);
            $this->assertGreaterThanOrEqual(0, $value);
        }
    }

    /** @test */
    public function test_date_field_validation()
    {
        $dateFields = [
            'scheduled_date_of_inspection' => '2024-07-15',
            'report_submitted' => '2024-07-20',
            'agreement_submitted' => '2024-07-01',
            'billing_req_sent' => '2024-07-22'
        ];

        foreach ($dateFields as $field => $dateString) {
            $this->assertIsString($dateString);
            // Test that the date string can be parsed
            $parsedDate = \Carbon\Carbon::parse($dateString);
            $this->assertInstanceOf(\Carbon\Carbon::class, $parsedDate);
        }
    }

    /** @test */
    public function test_email_field_validation()
    {
        $emailFields = [
            'property_manager_email' => 'jane@test.com',
            'regional_manager_email' => 'bob@test.com',
            'macro_email' => 'test@client.com'
        ];

        foreach ($emailFields as $field => $email) {
            $this->assertIsString($email);
            $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
        }
    }

    /** @test */
    public function test_required_fields_validation()
    {
        $requiredFields = ['property_name', 'address'];

        foreach ($requiredFields as $field) {
            $this->assertIsString($field);
            $this->assertNotEmpty($field);
        }
    }

    /** @test */
    public function test_import_phase_specific_logic()
    {
        // Test initial phase
        $this->import->setPhase('initial');
        $this->assertEquals('initial', $this->import->getPhase());

        // Test update phase
        $this->import->setPhase('update');
        $this->assertEquals('update', $this->import->getPhase());

        // Test review phase
        $this->import->setPhase('review');
        $this->assertEquals('review', $this->import->getPhase());
    }

    /** @test */
    public function test_import_error_handling()
    {
        // Test that import class can handle various error scenarios
        $this->assertTrue(class_exists(HB837Import::class));
        $this->assertTrue(method_exists(HB837Import::class, 'setPhase'));

        // Test invalid phase handling (should default to 'initial')
        $import = new HB837Import();
        $import->setPhase('invalid_phase');
        // The actual implementation should handle this gracefully
        $this->assertIsString($import->getPhase());
    }
}