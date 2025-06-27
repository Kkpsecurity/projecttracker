<?php

namespace Tests\Feature;

use App\Models\Backup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackupSystemFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test backup index page loads successfully
     */
    public function test_backup_index_page_loads(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/services/backup');

        $response->assertStatus(200);
    }

    /**
     * Test backup can be created with valid data
     */
    public function test_backup_can_be_created_with_valid_data(): void
    {
        $backupData = [
            'name' => 'Test Backup',
            'tables' => ['hb837', 'clients'],
        ];

        $response = $this->actingAs($this->user)
            ->post('/admin/services/backup/save', $backupData);

        $response->assertRedirect();
        $this->assertDatabaseHas('backups', [
            'name' => 'Test Backup',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test backup creation fails with invalid data
     */
    public function test_backup_creation_fails_with_invalid_data(): void
    {
        $invalidData = [
            'name' => '', // Empty name
            'tables' => [], // Empty tables array
        ];

        $response = $this->actingAs($this->user)
            ->post('/admin/services/backup/save', $invalidData);

        $response->assertSessionHasErrors(['tables']);
    }

    /**
     * Test backup creation requires authentication
     */
    public function test_backup_creation_requires_authentication(): void
    {
        $backupData = [
            'name' => 'Test Backup',
            'tables' => ['hb837'],
        ];

        $response = $this->post('/admin/services/backup/save', $backupData);
        $response->assertRedirect('/login');
    }

    /**
     * Test backup listing shows user's backups
     */
    public function test_backup_listing_shows_user_backups(): void
    {
        // Create some backups
        $backup1 = Backup::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'User Backup 1',
        ]);

        $backup2 = Backup::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'User Backup 2',
        ]);

        // Create backup for another user
        $otherUser = User::factory()->create();
        $otherBackup = Backup::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Backup',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/admin/services/backup');

        $response->assertStatus(200);
        $response->assertSee('User Backup 1');
        $response->assertSee('User Backup 2');
        $response->assertSee('Other User Backup'); // All backups visible in admin view
    }

    /**
     * Test backup download functionality
     */
    public function test_backup_download_requires_authentication(): void
    {
        $backup = Backup::factory()->create([
            'user_id' => $this->user->id,
            'filename' => 'test_backup.xlsx',
        ]);

        $response = $this->get("/admin/services/backup/download/{$backup->id}");
        $response->assertRedirect('/login');
    }

    /**
     * Test backup status updates work correctly
     */
    public function test_backup_status_can_be_tracked(): void
    {
        $backup = Backup::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $backup->status);

        $backup->update(['status' => 'completed']);
        $this->assertEquals('completed', $backup->fresh()->status);
    }
}
