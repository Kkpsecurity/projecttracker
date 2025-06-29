<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test user for login testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'test@projecttracker.com';
        $password = 'password123';

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            // Update password
            $existingUser->password = Hash::make($password);
            $existingUser->save();
            $this->info("Updated existing test user: {$email}");
        } else {
            // Create new user
            User::create([
                'name' => 'Test User',
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $this->info("Created new test user: {$email}");
        }

        $this->info("Login credentials:");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");

        return 0;
    }
}
