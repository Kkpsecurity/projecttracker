<?php

namespace Tests\Feature\Modules\HB837;

use App\Models\HB837;
use App\Models\User;
use App\Modules\HB837\Services\UploadService;
use App\Modules\HB837\Services\ImportService;
use App\Modules\HB837\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HB837ThreePhaseSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $uploadService;
    protected $importService;
    protected $exportService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create();

        // Initialize services
        $this->uploadService = app(UploadService::class);
        $this->importService = app(ImportService::class);
        $this->exportService = app(ExportService::class);

        // Setup test storage
        Storage::fake('local');
    }

    /** @test */
    public function it_can_complete_full_three_phase_workflow()
    {
        $this->actingAs($this->user);

        // Phase 1: Upload File
        $csvContent = "Property Name,Address,City,State,Zip\n";
        $csvContent .= "Test Property 1,123 Main St,Anytown,CA,12345\n";
        $csvContent .= "Test Property 2,456 Oak Ave,Somewhere,TX,67890\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        // Test file upload
        $response = $this->postJson('/modules/hb837/import/upload', [
            'file' => $file
        ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure([
                    'success',
                    'phase',
                    'file_info',
                    'structure',
                    'suggested_mappings',
                    'available_fields',
                    'required_fields'
                ]);

        $uploadData = $response->json();
        $this->assertEquals('mapping', $uploadData['phase']);

        // Phase 2: Field Mapping
        $mappings = [
            'Property Name' => 'property_name',
            'Address' => 'address',
            'City' => 'city',
            'State' => 'state',
            'Zip' => 'zip'
        ];

        $response = $this->postJson('/modules/hb837/import/map-fields', [
            'file_path' => $uploadData['file_info']['stored_path'],
            'mappings' => $mappings,
            'session_id' => $uploadData['file_info']['upload_session']
        ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure([
                    'success',
                    'phase',
                    'preview',
                    'validation'
                ]);

        $mappingData = $response->json();
        $this->assertEquals('validation', $mappingData['phase']);

        // Phase 3: Execute Import
        $response = $this->postJson('/modules/hb837/import/execute', [
            'file_path' => $uploadData['file_info']['stored_path'],
            'mappings' => $mappings,
            'options' => ['update_existing' => true]
        ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure([
                    'success',
                    'results',
                    'phase'
                ]);

        $importData = $response->json();
        $this->assertEquals('complete', $importData['phase']);
        $this->assertGreaterThan(0, $importData['results']['imported_count']);

        // Verify data was imported
        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property 1',
            'address' => '123 Main St',
            'city' => 'Anytown'
        ]);

        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property 2',
            'address' => '456 Oak Ave',
            'city' => 'Somewhere'
        ]);
    }

    /** @test */
    public function it_can_handle_validation_errors()
    {
        $this->actingAs($this->user);

        // Create CSV with invalid data (missing required fields)
        $csvContent = "Property Name,Address\n";
        $csvContent .= "Test Property,123 Main St\n"; // Missing city and zip

        $file = UploadedFile::fake()->createWithContent('invalid.csv', $csvContent);

        $response = $this->postJson('/modules/hb837/import/upload', [
            'file' => $file
        ]);

        $uploadData = $response->json();

        $mappings = [
            'Property Name' => 'property_name',
            'Address' => 'address'
        ];

        $response = $this->postJson('/modules/hb837/import/map-fields', [
            'file_path' => $uploadData['file_info']['stored_path'],
            'mappings' => $mappings,
            'session_id' => $uploadData['file_info']['upload_session']
        ]);

        $mappingData = $response->json();

        // Should have validation errors for missing required fields
        $this->assertFalse($mappingData['validation']['is_valid']);
        $this->assertGreaterThan(0, $mappingData['validation']['invalid_rows']);
    }

    /** @test */
    public function it_can_export_data()
    {
        $this->actingAs($this->user);

        // Create test data
        HB837::factory()->create([
            'property_name' => 'Export Test Property',
            'address' => '789 Export St',
            'city' => 'Export City',
            'state' => 'EX',
            'zip' => '99999'
        ]);

        $response = $this->postJson('/modules/hb837/export', [
            'format' => 'xlsx',
            'city' => 'Export City'
        ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure([
                    'success',
                    'file_path',
                    'download_url',
                    'statistics'
                ]);
    }

    /** @test */
    public function it_can_create_backup()
    {
        $this->actingAs($this->user);

        // Create test data
        HB837::factory(5)->create();

        $response = $this->postJson('/modules/hb837/export/backup');

        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure([
                    'success',
                    'file_path',
                    'download_url',
                    'statistics'
                ]);
    }

    /** @test */
    public function it_can_get_statistics()
    {
        $this->actingAs($this->user);

        // Create test data with different statuses
        HB837::factory()->create(['report_status' => 'completed']);
        HB837::factory()->create(['report_status' => 'in-progress']);
        HB837::factory()->create(['contracting_status' => 'quoted']);

        $response = $this->getJson('/modules/hb837/statistics');

        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure([
                    'success',
                    'statistics' => [
                        'total',
                        'active',
                        'completed',
                        'quoted'
                    ]
                ]);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/modules/hb837/');
        $response->assertStatus(302); // Redirect to login

        $response = $this->postJson('/modules/hb837/import/upload');
        $response->assertStatus(302); // Redirect to login
    }

    /** @test */
    public function it_validates_file_upload_requirements()
    {
        $this->actingAs($this->user);

        // Test without file
        $response = $this->postJson('/modules/hb837/import/upload');
        $response->assertStatus(422);

        // Test with invalid file type
        $file = UploadedFile::fake()->create('test.txt', 100);
        $response = $this->postJson('/modules/hb837/import/upload', [
            'file' => $file
        ]);
        $response->assertStatus(422);

        // Test with oversized file
        $file = UploadedFile::fake()->create('large.csv', 15000); // 15MB
        $response = $this->postJson('/modules/hb837/import/upload', [
            'file' => $file
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function it_can_access_module_dashboard()
    {
        $this->actingAs($this->user);

        $response = $this->get('/modules/hb837/');
        $response->assertStatus(200)
                ->assertViewIs('modules.hb837.index')
                ->assertViewHas(['statistics', 'consultants']);
    }

    /** @test */
    public function it_can_access_import_interface()
    {
        $this->actingAs($this->user);

        $response = $this->get('/modules/hb837/import/');
        $response->assertStatus(200)
                ->assertViewIs('modules.hb837.import.index');
    }
}
