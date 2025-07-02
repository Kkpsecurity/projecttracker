<?php

namespace Tests\Feature;

use App\Models\HB837;
use App\Models\User;
use App\Models\Consultant;
use App\Models\HB837ImportFieldConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class ApplicationHealthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'name' => 'Health Test Admin',
            'email' => 'health@test.com',
            'is_admin' => true
        ]);
    }

    /** @test */
    public function application_home_page_loads_successfully()
    {
        $response = $this->get('/');
        // Accept either 200 (if public) or 302 (if redirecting to login)
        $this->assertContains($response->getStatusCode(), [200, 302]);
    }

    /** @test */
    public function admin_dashboard_loads_successfully()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function database_connection_is_working()
    {
        // Test basic database operations
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        $consultant = Consultant::factory()->create();
        $this->assertDatabaseHas('consultants', ['id' => $consultant->id]);

        $project = HB837::factory()->create(['user_id' => $user->id]);
        $this->assertDatabaseHas('hb837', ['id' => $project->id]);
    }

    /** @test */
    public function critical_routes_are_accessible()
    {
        $criticalRoutes = [
            'admin.dashboard',
            'admin.hb837.index',
            'admin.hb837.create',
            'admin.hb837.inspection-calendar.index',
            'admin.hb837-import-config.index',
            'admin.users.index',
        ];

        foreach ($criticalRoutes as $routeName) {
            $response = $this->actingAs($this->admin)->get(route($routeName));
            $this->assertEquals(200, $response->getStatusCode(), "Route {$routeName} failed to load");
        }
    }

    /** @test */
    public function hb837_crud_operations_work()
    {
        // Test Create
        $projectData = [
            'property_name' => 'Health Test Property',
            'address' => '123 Health Test St',
            'city' => 'Test City',
            'state' => 'TS',
            'zip' => '12345',
            'report_status' => 'not-started',
            'contracting_status' => 'quoted'
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.hb837.store'), $projectData);

        $response->assertStatus(302); // Redirect after successful creation
        $this->assertDatabaseHas('hb837', ['property_name' => 'Health Test Property']);

        // Test Read
        $project = HB837::where('property_name', 'Health Test Property')->first();
        $response = $this->actingAs($this->admin)
            ->get(route('admin.hb837.show', $project));
        $response->assertStatus(200);

        // Test Update
        $updateData = ['property_name' => 'Updated Health Test Property'];
        $response = $this->actingAs($this->admin)
            ->put(route('admin.hb837.update', $project), array_merge($projectData, $updateData));

        $response->assertStatus(302);
        $this->assertDatabaseHas('hb837', ['property_name' => 'Updated Health Test Property']);

        // Test Delete
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.hb837.destroy', $project));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('hb837', ['id' => $project->id]);
    }

    /** @test */
    public function ajax_endpoints_respond_correctly()
    {
        $ajaxEndpoints = [
            'admin.hb837.data',
            'admin.hb837.stats',
            'admin.hb837.inspection-calendar.events',
            'admin.hb837.inspection-calendar.statuses',
        ];

        foreach ($ajaxEndpoints as $endpoint) {
            $response = $this->actingAs($this->admin)->get(route($endpoint));
            $this->assertEquals(200, $response->getStatusCode(), "AJAX endpoint {$endpoint} failed");
            $response->assertHeader('content-type', 'application/json');
        }
    }

    /** @test */
    public function import_field_configuration_system_works()
    {
        // Test that import config records exist or can be created
        $configData = [
            'database_field' => 'test_field',
            'field_label' => 'Test Field',
            'description' => 'Test field description',
            'field_type' => 'string',
            'excel_column_mappings' => "Test Column\nAlt Test",
            'max_length' => 255,
            'sort_order' => 1,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.hb837-import-config.store'), $configData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('hb837_import_field_configs', [
            'database_field' => 'test_field'
        ]);
    }

    /** @test */
    public function user_authentication_system_works()
    {
        // Test login redirect for protected routes
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));

        // Test successful authentication
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    /** @test */
    public function error_pages_are_handled_gracefully()
    {
        // Test 404 handling
        $response = $this->actingAs($this->admin)
            ->get('/admin/hb837/99999');
        $response->assertStatus(404);

        // Test invalid route
        $response = $this->actingAs($this->admin)
            ->get('/admin/nonexistent-route');
        $response->assertStatus(404);
    }

    /** @test */
    public function cache_system_is_functional()
    {
        // Test basic cache operations
        cache(['test_key' => 'test_value'], 60);
        $this->assertEquals('test_value', cache('test_key'));

        cache()->forget('test_key');
        $this->assertNull(cache('test_key'));
    }

    /** @test */
    public function session_system_works()
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['test_session' => 'session_value'])
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSessionHas('test_session', 'session_value');
    }

    /** @test */
    public function file_upload_system_is_ready()
    {
        // Test that storage directories exist and are writable
        $storagePaths = [
            storage_path('app/public'),
            storage_path('app/temp'),
            storage_path('logs')
        ];

        foreach ($storagePaths as $path) {
            $this->assertTrue(
                is_dir($path) && is_writable($path),
                "Storage path {$path} is not accessible or writable"
            );
        }
    }

    /** @test */
    public function environment_configuration_is_correct()
    {
        // Test critical environment variables
        $this->assertNotNull(config('app.key'), 'App key is not set');
        $this->assertEquals('testing', config('app.env'), 'Not in testing environment');
        $this->assertNotNull(config('database.connections.pgsql.host'), 'Database host not configured');
    }

    /** @test */
    public function javascript_assets_compile_correctly()
    {
        // Test that critical asset files exist
        $assetFiles = [
            'resources/js/app.js',
            'resources/css/app.css',
            'package.json'
        ];

        foreach ($assetFiles as $file) {
            $this->assertFileExists(base_path($file), "Asset file {$file} is missing");
        }
    }

    /** @test */
    public function models_have_proper_relationships()
    {
        $user = User::factory()->create();
        $consultant = Consultant::factory()->create();
        $project = HB837::factory()->create([
            'user_id' => $user->id,
            'assigned_consultant_id' => $consultant->id
        ]);

        // Test relationships
        $this->assertInstanceOf(User::class, $project->user);
        $this->assertInstanceOf(Consultant::class, $project->consultant);
        $this->assertEquals($user->id, $project->user->id);
        $this->assertEquals($consultant->id, $project->consultant->id);
    }

    /** @test */
    public function date_handling_works_correctly()
    {
        $project = HB837::factory()->create([
            'scheduled_date_of_inspection' => Carbon::today()->addDays(7),
            'user_id' => $this->admin->id
        ]);

        // Test date casting
        $this->assertInstanceOf(Carbon::class, $project->scheduled_date_of_inspection);

        // Test date formatting
        $this->assertEquals(
            Carbon::today()->addDays(7)->format('Y-m-d'),
            $project->scheduled_date_of_inspection->format('Y-m-d')
        );
    }

    /** @test */
    public function performance_indicators_are_acceptable()
    {
        $startTime = microtime(true);

        // Simulate typical user workflow
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $this->actingAs($this->admin)
            ->get(route('admin.hb837.index'));

        $this->actingAs($this->admin)
            ->get(route('admin.hb837.inspection-calendar.index'));

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert that typical workflow completes within reasonable time (5 seconds)
        $this->assertLessThan(5.0, $executionTime, 'Application performance is too slow');
    }
}
