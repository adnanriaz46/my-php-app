<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:registration {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user registration and email verification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing registration for: {$email}");
        
        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }
        
        // Create user data
        $userData = [
            'uuid' => (string) Str::uuid(),
            'name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone_number' => '(123) 456-7890',
            'email' => $email,
            'password' => Hash::make('password123'),
        ];
        
        $this->info("Creating user...");
        
        try {
            $user = User::create($userData);
            $this->info("✓ User created: {$user->name}");
            
            // Check if user implements MustVerifyEmail
            $this->info("User implements MustVerifyEmail: " . ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail ? 'Yes' : 'No'));
            
            // Check verification status
            $this->info("Email verified: " . ($user->hasVerifiedEmail() ? 'Yes' : 'No'));
            $this->info("Email verified at: " . ($user->email_verified_at ?? 'Never'));
            
            // Test sending verification email
            $this->info("Sending verification email...");
            $user->sendEmailVerificationNotification();
            $this->info("✓ Verification email sent!");
            
            // Clean up - delete the test user
            $this->info("Cleaning up - deleting test user...");
            $user->delete();
            $this->info("✓ Test user deleted");
            
        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
            return 1;
        }
        
        $this->info("Registration test completed!");
        return 0;
    }
} 