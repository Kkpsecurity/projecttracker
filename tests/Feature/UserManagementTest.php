<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user for testing
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function admin_can_view_users_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index_new');
        $response->assertViewHas('users');
    }

    /** @test */
    public function admin_can_view_create_user_form()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $userData = [
            'name' => 'New Test User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User created successfully');

        $this->assertDatabaseHas('users', [
            'name' => 'New Test User',
            'email' => 'newuser@test.com',
        ]);

        // Verify the user's email is auto-verified
        $user = User::where('email', 'newuser@test.com')->first();
        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function admin_can_view_user_details()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $user));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.show');
        $response->assertViewHas('user', $user);
    }

    /** @test */
    public function admin_can_view_edit_user_form()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.edit', $user));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
        $response->assertViewHas('user', $user);
    }

    /** @test */
    public function admin_can_update_user()
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@test.com',
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@test.com',
            'email_verified' => '1',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.update', $user), $updateData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User updated successfully');

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updated@test.com', $user->email);
        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function admin_can_update_user_password()
    {
        $user = User::factory()->create();
        $originalPassword = $user->password;

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.update', $user), $updateData);

        $response->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertNotEquals($originalPassword, $user->password);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function admin_can_unverify_user_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            // Not including email_verified checkbox (unchecked)
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.update', $user), $updateData);

        $response->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.delete', $user));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User deleted successfully');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function guest_cannot_access_user_management()
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.users.create'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function user_creation_requires_unique_email()
    {
        $existingUser = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => $existingUser->email, // Duplicate email
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function user_creation_requires_password_confirmation()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => 'test@test.com',
                'password' => 'password123',
                'password_confirmation' => 'different_password',
            ]);

        $response->assertSessionHasErrors(['password']);
    }
}
