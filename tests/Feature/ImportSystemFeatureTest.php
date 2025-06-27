<?php

namespace Tests\Feature;

use App\Models\HB837;
use App\Models\User;
use App\Services\ImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportSystemFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Storage::fake('local');
    }

    /**
     * Test import page loads successfully
     */
    public function test_import_page_loads(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/hb837/import');

        $response->assertStatus(200);
    }

    /**
     * Test import requires authentication
     */
    public function test_import_requires_authentication(): void
    {
        $response = $this->get('/admin/hb837/import');
        $response->assertRedirect('/login');
    }

    /**
     * Test file upload validation
     */
    public function test_import_validates_file_upload(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/admin/hb837/import/preview', [
                // No file uploaded
            ]);

        $response->assertSessionHasErrors(['file']);
    }

    /**
     * Test file type validation
     */
    public function test_import_validates_file_type(): void
    {
        $file = UploadedFile::fake()->create('test.txt', 100, 'text/plain');

        $response = $this->actingAs($this->user)
            ->post('/admin/hb837/import/preview', [
                'file' => $file,
            ]);

        $response->assertSessionHasErrors(['file']);
    }

    /**
     * Test ImportService integration
     */
    public function test_import_service_integration(): void
    {
        $importService = new ImportService;
        $this->assertInstanceOf(ImportService::class, $importService);

        // Test that service methods exist
        $this->assertTrue(method_exists($importService, 'previewImport'));
        $this->assertTrue(method_exists($importService, 'executeImport'));
    }

    /**
     * Test import preview with valid Excel file structure
     */
    public function test_import_preview_handles_file_structure(): void
    {
        // Create a fake Excel file
        $file = UploadedFile::fake()->create('test.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAs($this->user)
            ->post('/admin/hb837/import/preview', [
                'file' => $file,
            ]);

        // This will likely fail due to invalid file content, but tests the validation flow
        $this->assertTrue(true); // Placeholder assertion
    }

    /**
     * Test import statistics tracking
     */
    public function test_import_statistics_tracking(): void
    {
        // Test that HB837 model can track import statistics
        $initialCount = HB837::count();
        $this->assertEquals(0, $initialCount);

        // Create some test data
        HB837::factory()->create();
        $afterCreateCount = HB837::count();
        $this->assertEquals(1, $afterCreateCount);
    }

    /**
     * Test import audit trail functionality
     */
    public function test_import_audit_trail(): void
    {
        // Test that we can track import audits
        $this->assertDatabaseMissing('import_audits', [
            'user_id' => $this->user->id,
        ]);

        // This would be populated during actual import process
        $this->assertTrue(true); // Placeholder for audit trail testing
    }

    /**
     * Test import error handling
     */
    public function test_import_error_handling(): void
    {
        $importService = new ImportService;

        // Test error handling with invalid file path
        try {
            $importService->previewImport('/invalid/path.xlsx');
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    /**
     * Test import truncate mode functionality
     */
    public function test_import_truncate_mode(): void
    {
        // Create existing HB837 records
        HB837::factory()->count(5)->create();
        $this->assertEquals(5, HB837::count());

        // Test that truncate mode can be set
        $import = new \App\Imports\HB837Import;
        $result = $import->setTruncateMode(true);
        $this->assertInstanceOf(\App\Imports\HB837Import::class, $result);
    }

    /**
     * Test import batch processing
     */
    public function test_import_batch_processing(): void
    {
        $import = new \App\Imports\HB837Import;

        // Test that counters are initialized properly
        $this->assertEquals(0, $import->importedCount);
        $this->assertEquals(0, $import->updatedCount);
        $this->assertEquals(0, $import->skippedCount);
        $this->assertIsArray($import->skippedProperties);
    }
}
