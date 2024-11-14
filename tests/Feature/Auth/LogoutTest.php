<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LogoutTest extends TestCase
{
//    use DatabaseTransactions;
    /**
     * Test successful logout.
     *
     * @return void
     */
    public function test_logout_success()
    {
        // Create a user and authenticate with Passport
        $user = User::factory()->create();

        Passport::actingAs($user);

        // Make a POST request to logout endpoint
        $response = $this->postJson('api/logout');

        // Assert response status and structure
        $response->assertStatus(200);
        // Assert the response structure
        $response->assertJson([
            'success' => true,
            'statusCode' => 200,
            'message' => 'Logged out successfully.',
            ]);


    }

    /**
     * Test logout without an authenticated user.
     *
     * @return void
     */
    public function test_logout_without_authentication()
    {
        // Make a POST request to logout endpoint without authentication
        $response = $this->postJson('/api/logout');

        // Assert unauthorized status
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
