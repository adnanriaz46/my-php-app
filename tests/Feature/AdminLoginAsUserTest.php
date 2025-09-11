<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginAsUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_as_user()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'user_type' => User::ADMIN,
        ]);

        // Create a regular user
        $user = User::factory()->create([
            'user_type' => User::FREE,
        ]);

        // Login as admin
        $this->actingAs($admin);

        // Attempt to login as the regular user
        $response = $this->postJson(route('admin.users.login-as', $user->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => "Now logged in as {$user->name}",
            ]);

        // Verify we're now logged in as the target user
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_cannot_login_as_another_admin()
    {
        // Create two admin users
        $admin1 = User::factory()->create([
            'user_type' => User::ADMIN,
        ]);

        $admin2 = User::factory()->create([
            'user_type' => User::ADMIN,
        ]);

        // Login as first admin
        $this->actingAs($admin1);

        // Attempt to login as the second admin
        $response = $this->postJson(route('admin.users.login-as', $admin2->id));

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Cannot impersonate another admin user',
            ]);

        // Verify we're still logged in as the first admin
        $this->assertAuthenticatedAs($admin1);
    }

    public function test_non_admin_cannot_login_as_user()
    {
        // Create a regular user
        $regularUser = User::factory()->create([
            'user_type' => User::FREE,
        ]);

        // Create another user to attempt to impersonate
        $targetUser = User::factory()->create([
            'user_type' => User::FREE,
        ]);

        // Login as regular user
        $this->actingAs($regularUser);

        // Attempt to login as the target user
        $response = $this->postJson(route('admin.users.login-as', $targetUser->id));

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);

        // Verify we're still logged in as the regular user
        $this->assertAuthenticatedAs($regularUser);
    }

    public function test_admin_can_stop_impersonation()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'user_type' => User::ADMIN,
        ]);

        // Create a regular user
        $user = User::factory()->create([
            'user_type' => User::FREE,
        ]);

        // Login as admin
        $this->actingAs($admin);

        // Login as the regular user
        $this->postJson(route('admin.users.login-as', $user->id));

        // Verify we're now logged in as the target user
        $this->assertAuthenticatedAs($user);

        // Stop impersonation
        $response = $this->postJson(route('stop-impersonation'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Returned to admin account',
            ]);

        // Verify we're back to the admin user
        $this->assertAuthenticatedAs($admin);
    }
} 