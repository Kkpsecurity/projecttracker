<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\HB837;
use App\Models\Consultant;
use App\Models\HB837File;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class HB837ControllerTest extends TestCase
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

    public function test_can_view_hb837_index()
    {
        // Create test HB837 records
        HB837::factory()->count(5)->create();

        $response = $this->get(route('admin.hb837.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.hb837.index');
        $response->assertViewHas('stats');
        $response->assertSee('HB837');
    }

    public function test_can_view_hb837_with_different_tabs()
    {
        // Create records with different statuses
        HB837::factory()->create([
            'report_status' => 'in-progress',
            'contracting_status' => 'executed'
        ]);
        
        HB837::factory()->create([
            'contracting_status' => 'quoted'
        ]);
        
        HB837::factory()->create([
            'report_status' => 'completed'
        ]);

        // Test active tab
        $response = $this->get(route('admin.hb837.index', 'active'));
        $response->assertStatus(200);

        // Test quoted tab
        $response = $this->get(route('admin.hb837.index', 'quoted'));
        $response->assertStatus(200);

        // Test completed tab
        $response = $this->get(route('admin.hb837.index', 'completed'));
        $response->assertStatus(200);
    }

    public function test_datatables_ajax_response()
    {
        HB837::factory()->count(3)->create();

        $response = $this->get(route('admin.hb837.data'), [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'action',
                    'property_name',
                    'created_at'
                ]
            ]
        ]);
    }

    public function test_can_create_new_hb837_record()
    {
        $response = $this->get(route('admin.hb837.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.hb837.create');
        $response->assertViewHas(['consultants', 'propertyTypes', 'securityGauge']);
    }

    public function test_can_store_new_hb837_record()
    {
        $data = [
            'property_name' => 'Test Property',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'CA',
            'zip' => '90210',
            'property_type' => 'garden',
            'units' => 100,
            'quoted_price' => 25000,
            'sub_fees_estimated_expenses' => 5000,
            'assigned_consultant_id' => $this->consultant->id,
            'report_status' => 'not-started',
            'contracting_status' => 'quoted'
        ];

        $response = $this->post(route('admin.hb837.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('hb837', [
            'property_name' => 'Test Property',
            'address' => '123 Test Street',
            'project_net_profit' => 20000 // Should be calculated
        ]);
    }

    public function test_can_view_hb837_record()
    {
        $hb837 = HB837::factory()->create();

        $response = $this->get(route('admin.hb837.show', $hb837));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.hb837.show');
        $response->assertViewHas('hb837');
    }

    public function test_can_edit_hb837_record()
    {
        $hb837 = HB837::factory()->create();

        $response = $this->get(route('admin.hb837.edit', $hb837));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.hb837.edit');
        $response->assertViewHas(['hb837', 'consultants', 'propertyTypes', 'securityGauge']);
    }

    public function test_can_update_hb837_record()
    {
        $hb837 = HB837::factory()->create([
            'property_name' => 'Original Name'
        ]);

        $updateData = [
            'property_name' => 'Updated Property Name',
            'address' => $hb837->address,
            'quoted_price' => 30000,
            'sub_fees_estimated_expenses' => 6000
        ];

        $response = $this->put(route('admin.hb837.update', $hb837), $updateData);

        $response->assertRedirect();
        $hb837->refresh();
        
        $this->assertEquals('Updated Property Name', $hb837->property_name);
        $this->assertEquals(24000, $hb837->project_net_profit); // Should be recalculated
    }

    public function test_can_delete_hb837_record()
    {
        $hb837 = HB837::factory()->create();

        $response = $this->delete(route('admin.hb837.destroy', $hb837));

        $response->assertRedirect(route('admin.hb837.index'));
        $this->assertDatabaseMissing('hb837', ['id' => $hb837->id]);
    }

    public function test_bulk_actions_delete()
    {
        $records = HB837::factory()->count(3)->create();
        $ids = $records->pluck('id')->toArray();

        $response = $this->post(route('admin.hb837.bulk-action'), [
            'action' => 'delete',
            'selected_ids' => $ids
        ]);

        $response->assertJson(['success' => true]);
        $this->assertEquals(0, HB837::count());
    }

    public function test_bulk_actions_status_update()
    {
        $records = HB837::factory()->count(3)->create([
            'report_status' => 'not-started'
        ]);
        $ids = $records->pluck('id')->toArray();

        $response = $this->post(route('admin.hb837.bulk-action'), [
            'action' => 'status_update',
            'selected_ids' => $ids,
            'bulk_status' => 'in-progress'
        ]);

        $response->assertJson(['success' => true]);
        
        $records->each(function ($record) {
            $record->refresh();
            $this->assertEquals('in-progress', $record->report_status);
        });
    }

    public function test_bulk_actions_consultant_assign()
    {
        $records = HB837::factory()->count(3)->create();
        $ids = $records->pluck('id')->toArray();

        $response = $this->post(route('admin.hb837.bulk-action'), [
            'action' => 'consultant_assign',
            'selected_ids' => $ids,
            'bulk_consultant_id' => $this->consultant->id
        ]);

        $response->assertJson(['success' => true]);
        
        $records->each(function ($record) {
            $record->refresh();
            $this->assertEquals($this->consultant->id, $record->assigned_consultant_id);
        });
    }

    public function test_quick_status_update()
    {
        $hb837 = HB837::factory()->create(['report_status' => 'not-started']);

        $response = $this->patch(route('admin.hb837.status', $hb837), [
            'status' => 'in-progress'
        ]);

        $response->assertJson(['success' => true]);
        $hb837->refresh();
        $this->assertEquals('in-progress', $hb837->report_status);
    }

    public function test_file_upload()
    {
        $hb837 = HB837::factory()->create();
        $file = UploadedFile::fake()->create('test-document.pdf', 1000);

        $response = $this->post(route('admin.hb837.files.upload', $hb837), [
            'file' => $file,
            'description' => 'Test document upload'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hb837_files', [
            'hb837_id' => $hb837->id,
            'filename' => 'test-document.pdf',
            'description' => 'Test document upload'
        ]);
    }

    public function test_file_download()
    {
        $hb837 = HB837::factory()->create();
        
        // Create a test file in storage
        \Storage::fake('public');
        $testFile = UploadedFile::fake()->create('test.pdf', 100);
        $path = $testFile->storeAs('hb837/' . $hb837->id, 'test.pdf', 'public');
        
        $hb837File = HB837File::create([
            'hb837_id' => $hb837->id,
            'filename' => 'test.pdf',
            'stored_filename' => 'test.pdf',
            'file_path' => $path,
            'file_size' => 100,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $this->user->id
        ]);

        $response = $this->get(route('admin.hb837.files.download', $hb837File));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

    public function test_file_delete()
    {
        $hb837 = HB837::factory()->create();
        
        \Storage::fake('public');
        $testFile = UploadedFile::fake()->create('test.pdf', 100);
        $path = $testFile->storeAs('hb837/' . $hb837->id, 'test.pdf', 'public');
        
        $hb837File = HB837File::create([
            'hb837_id' => $hb837->id,
            'filename' => 'test.pdf',
            'stored_filename' => 'test.pdf',
            'file_path' => $path,
            'file_size' => 100,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $this->user->id
        ]);

        $response = $this->delete(route('admin.hb837.files.delete', $hb837File));

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('hb837_files', ['id' => $hb837File->id]);
    }

    public function test_search_functionality()
    {
        HB837::factory()->create([
            'property_name' => 'Sunset Gardens',
            'address' => '123 Main Street'
        ]);
        
        HB837::factory()->create([
            'property_name' => 'Downtown Plaza',
            'address' => '456 Oak Avenue'
        ]);

        $response = $this->get(route('api.hb837.search', ['q' => 'Sunset']));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['property_name' => 'Sunset Gardens']);

        // Test address search
        $response = $this->get(route('api.hb837.search', ['q' => 'Oak']));
        $response->assertJsonFragment(['property_name' => 'Downtown Plaza']);
    }

    public function test_crime_risk_color_coding()
    {
        $hb837 = HB837::factory()->create([
            'securitygauge_crime_risk' => 'High'
        ]);

        $response = $this->get(route('admin.hb837.data'), [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        
        // Check that the response contains color-coded crime risk
        $this->assertStringContainsString('#f2a36e', $data['data'][0]['securitygauge_crime_risk']);
        $this->assertStringContainsString('High', $data['data'][0]['securitygauge_crime_risk']);
    }

    public function test_report_status_color_coding()
    {
        $hb837 = HB837::factory()->create([
            'report_status' => 'in-progress'
        ]);

        $response = $this->get(route('admin.hb837.data'), [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        
        // Check that the response contains color-coded report status
        $this->assertStringContainsString('#fff3cd', $data['data'][0]['report_status']);
        $this->assertStringContainsString('In progress', $data['data'][0]['report_status']);
    }

    public function test_validation_errors_on_store()
    {
        // Test missing required fields
        $response = $this->post(route('admin.hb837.store'), []);

        $response->assertSessionHasErrors(['property_name', 'address']);

        // Test invalid data types
        $response = $this->post(route('admin.hb837.store'), [
            'property_name' => 'Test Property',
            'address' => '123 Test St',
            'units' => 'invalid_number',
            'quoted_price' => 'invalid_price'
        ]);

        $response->assertSessionHasErrors(['units', 'quoted_price']);
    }

    public function test_overdue_inspection_highlighting()
    {
        $overdueDate = now()->subDays(5)->format('Y-m-d');
        
        $hb837 = HB837::factory()->create([
            'scheduled_date_of_inspection' => $overdueDate,
            'report_status' => 'in-progress' // Not completed
        ]);

        $response = $this->get(route('admin.hb837.data'), [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        
        // Check that overdue dates are highlighted in red
        $this->assertStringContainsString('text-danger', $data['data'][0]['scheduled_date_of_inspection']);
        $this->assertStringContainsString('font-weight-bold', $data['data'][0]['scheduled_date_of_inspection']);
    }
}
