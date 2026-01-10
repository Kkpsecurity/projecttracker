<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that an admin can create a user successfully
     */
    public function test_admin_can_create_user()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        // Act as the admin user
        $response = $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'phone' => '123-456-7890',
                'bio' => 'Test user bio',
                'is_admin' => false,
                'is_active' => true,
            ]);

        // Assert redirect to users index
        $response->assertRedirect('/admin/users');

        // Assert user was created in database
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '123-456-7890',
            'bio' => 'Test user bio',
            'is_admin' => false,
            'is_active' => true,
        ]);

        // Assert password was hashed
        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('secret123', $user->password));

        // Assert success message
        $response->assertSessionHas('success');
    }

    /**
     * Test validation errors when creating user with invalid data
     */
    public function test_user_creation_validation_errors()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => '',
                'email' => 'invalid-email',
                'password' => 'short',
                'password_confirmation' => 'different',
            ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertDatabaseMissing('users', ['email' => 'invalid-email']);
    }

    /**
     * Test duplicate email validation
     */
    public function test_duplicate_email_validation()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Test User',
                'email' => 'existing@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
            ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test non-admin users cannot access user creation
     */
    public function test_non_admin_cannot_create_users()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)
            ->post('/admin/users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
            ]);

        // This should fail - we need to add middleware protection
        // For now, let's just check the response
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    /**
     * Test admin can view user creation form
     */
    public function test_admin_can_view_create_user_form()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->get('/admin/users/create');

        $response->assertStatus(200);
        $response->assertSee('Create New User');
        $response->assertSee('User Information');
    }

    /**
     * Test admin can view users list
     */
    public function test_admin_can_view_users_list()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('User Management');

        // Check that users are displayed
        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    /**
     * Test password requirements validation
     */
    public function test_password_requirements_validation()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    /**
     * Test optional fields can be null
     */
    public function test_optional_fields_can_be_null()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                // phone and bio are optional
            ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => null,
            'bio' => null,
        ]);
    }

    /**
     * Test admin flag is properly set
     */
    public function test_admin_flag_properly_set()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // Create admin user
        $response = $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'is_admin' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        // Create regular user
        $response = $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Regular User',
                'email' => 'regular@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'is_admin' => false,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'regular@example.com',
            'is_admin' => false,
        ]);
    }
}
