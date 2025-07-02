<?php

namespace Tests\Feature\Admin\HB837;

use App\Models\HB837;
use App\Models\User;
use App\Models\Consultant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class InspectionCalendarTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $consultant;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'is_admin' => true
        ]);

        // Create test consultant
        $this->consultant = Consultant::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Consultant',
            'email' => 'consultant@test.com'
        ]);
    }

    /** @test */
    public function it_can_display_inspection_calendar_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.hb837.inspection-calendar.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.hb837.inspection-calendar.index')
            ->assertSee('Inspection Schedule Calendar')
            ->assertSee('HB837 Inspection Schedule');
    }

    /** @test */
    public function it_can_fetch_calendar_events_with_scheduled_inspections()
    {
        // Create HB837 projects with scheduled inspection dates
        $project1 = HB837::factory()->create([
            'property_name' => 'Test Property 1',
            'address' => '123 Test St',
            'scheduled_date_of_inspection' => Carbon::today()->addDays(7),
            'report_status' => 'not-started',
            'assigned_consultant_id' => $this->consultant->id,
            'user_id' => $this->user->id
        ]);

        $project2 = HB837::factory()->create([
            'property_name' => 'Test Property 2',
            'address' => '456 Test Ave',
            'scheduled_date_of_inspection' => Carbon::today()->addDays(14),
            'report_status' => 'in-progress',
            'assigned_consultant_id' => $this->consultant->id,
            'user_id' => $this->user->id
        ]);

        // Create project without scheduled date (should not appear)
        HB837::factory()->create([
            'property_name' => 'Unscheduled Property',
            'scheduled_date_of_inspection' => null,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.hb837.inspection-calendar.events', [
                'status' => 'all',
                'start' => Carbon::today()->startOfMonth()->toISOString(),
                'end' => Carbon::today()->endOfMonth()->toISOString()
            ]));

        $response->assertStatus(200)
            ->assertJsonCount(2) // Only projects with scheduled dates
            ->assertJsonFragment([
                'title' => 'Test Property 1'
            ])
            ->assertJsonFragment([
                'title' => 'Test Property 2'
            ]);
    }

    /** @test */
    public function it_can_filter_events_by_status()
    {
        HB837::factory()->create([
            'property_name' => 'In Progress Property',
            'scheduled_date_of_inspection' => Carbon::today()->addDays(5),
            'report_status' => 'in-progress',
            'user_id' => $this->user->id
        ]);

        HB837::factory()->create([
            'property_name' => 'Completed Property',
            'scheduled_date_of_inspection' => Carbon::today()->addDays(10),
            'report_status' => 'completed',
            'user_id' => $this->user->id
        ]);

        // Filter by in-progress status
        $response = $this->actingAs($this->user)
            ->get(route('admin.hb837.inspection-calendar.events', [
                'status' => 'in-progress',
                'start' => Carbon::today()->startOfMonth()->toISOString(),
                'end' => Carbon::today()->endOfMonth()->toISOString()
            ]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'In Progress Property'])
            ->assertJsonMissing(['title' => 'Completed Property']);
    }

    /** @test */
    public function it_can_fetch_available_statuses()
    {
        // Create projects with different statuses
        HB837::factory()->create([
            'scheduled_date_of_inspection' => Carbon::today(),
            'report_status' => 'not-started',
            'user_id' => $this->user->id
        ]);

        HB837::factory()->create([
            'scheduled_date_of_inspection' => Carbon::today(),
            'report_status' => 'in-progress',
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.hb837.inspection-calendar.statuses'));

        $response->assertStatus(200)
            ->assertJsonFragment(['not-started'])
            ->assertJsonFragment(['in-progress']);
    }

    /** @test */
    public function it_can_fetch_project_details()
    {
        $project = HB837::factory()->create([
            'property_name' => 'Detailed Property',
            'address' => '789 Detail St',
            'city' => 'Test City',
            'state' => 'TS',
            'zip' => '12345',
            'scheduled_date_of_inspection' => Carbon::today()->addDays(3),
            'report_status' => 'in-progress',
            'contracting_status' => 'quoted',
            'quoted_price' => 5000.00,
            'units' => 10,
            'notes' => 'Test project notes',
            'assigned_consultant_id' => $this->consultant->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.hb837.inspection-calendar.project', $project->id));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'project' => [
                    'id',
                    'property_name',
                    'address',
                    'city',
                    'state',
                    'zip',
                    'scheduled_date_of_inspection',
                    'report_status',
                    'contracting_status',
                    'quoted_price',
                    'units',
                    'notes',
                    'consultant',
                    'created_by',
                    'updated_at'
                ]
            ])
            ->assertJsonFragment([
                'success' => true,
                'property_name' => 'Detailed Property',
                'address' => '789 Detail St'
            ]);
    }

    /** @test */
    public function it_can_update_inspection_date()
    {
        $project = HB837::factory()->create([
            'property_name' => 'Update Test Property',
            'scheduled_date_of_inspection' => Carbon::today()->addDays(5),
            'user_id' => $this->user->id
        ]);

        $newDate = Carbon::today()->addDays(10)->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->putJson(route('admin.hb837.inspection-calendar.update-date', $project->id), [
                'scheduled_date_of_inspection' => $newDate
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'message' => 'Inspection date updated successfully'
            ]);

        // Verify the date was updated in database
        $project->refresh();
        $this->assertEquals($newDate, $project->scheduled_date_of_inspection->format('Y-m-d'));
    }

    /** @test */
    public function it_validates_inspection_date_update_request()
    {
        $project = HB837::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Test with invalid date
        $response = $this->actingAs($this->user)
            ->putJson(route('admin.hb837.inspection-calendar.update-date', $project->id), [
                'scheduled_date_of_inspection' => 'invalid-date'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['scheduled_date_of_inspection']);

        // Test with missing date
        $response = $this->actingAs($this->user)
            ->putJson(route('admin.hb837.inspection-calendar.update-date', $project->id), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['scheduled_date_of_inspection']);
    }

    /** @test */
    public function it_requires_authentication_for_calendar_access()
    {
        $response = $this->get(route('admin.hb837.inspection-calendar.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.hb837.inspection-calendar.events'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_handles_nonexistent_project_details_gracefully()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.hb837.inspection-calendar.project', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function it_handles_date_range_filtering()
    {
        // Create projects in different months
        $currentMonth = HB837::factory()->create([
            'property_name' => 'Current Month',
            'scheduled_date_of_inspection' => Carbon::today(),
            'user_id' => $this->user->id
        ]);

        $nextMonth = HB837::factory()->create([
            'property_name' => 'Next Month',
            'scheduled_date_of_inspection' => Carbon::today()->addMonth(),
            'user_id' => $this->user->id
        ]);

        // Request only current month
        $response = $this->actingAs($this->user)
            ->get(route('admin.hb837.inspection-calendar.events', [
                'start' => Carbon::today()->startOfMonth()->toISOString(),
                'end' => Carbon::today()->endOfMonth()->toISOString()
            ]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'Current Month'])
            ->assertJsonMissing(['title' => 'Next Month']);
    }

    /** @test */
    public function it_includes_consultant_information_in_events()
    {
        $project = HB837::factory()->create([
            'property_name' => 'Consultant Test',
            'scheduled_date_of_inspection' => Carbon::today(),
            'assigned_consultant_id' => $this->consultant->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.hb837.inspection-calendar.events', [
                'start' => Carbon::today()->startOfMonth()->toISOString(),
                'end' => Carbon::today()->endOfMonth()->toISOString()
            ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'consultant' => $this->consultant->name
            ]);
    }

    /** @test */
    public function it_handles_projects_without_consultants()
    {
        $project = HB837::factory()->create([
            'property_name' => 'No Consultant',
            'scheduled_date_of_inspection' => Carbon::today(),
            'assigned_consultant_id' => null,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.hb837.inspection-calendar.events', [
                'start' => Carbon::today()->startOfMonth()->toISOString(),
                'end' => Carbon::today()->endOfMonth()->toISOString()
            ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'consultant' => 'Unassigned'
            ]);
    }
}
