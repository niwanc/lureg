<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_returns_authenticated_user_info(): void
    {
        // Create a user instance
        $user = User::factory()->create();

        // Act as the authenticated user
        Passport::actingAs($user);

        // Send a GET request to the 'user' route
        $response = $this->getJson('/api/user');

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Authenticated user info.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        // Add any other fields present in the UserResource
                    ],
                ]
            ]);
    }

    public function test_unauthenticated_user_receives_401()
    {
        // Send a GET request to the 'user' route without authentication
        $response = $this->getJson('/api/user');

        // Assert that the response has a 401 Unauthorized status
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_user_data_integrity_check()
    {
        // Create a user instance
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);

        // Act as the authenticated user
        Passport::actingAs($user);

        // Send a GET request to the 'user' route
        $response = $this->getJson('/api/user');

        // Assert that response includes expected user data
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user->id,
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
            ]);
    }
}
