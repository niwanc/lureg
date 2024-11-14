<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Repositories\OAuthRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
   use DatabaseTransactions;

    public function test_user_registration_successful_with_token(): void
    {
        // Arrange: Mock Carbon::now() to return a specific date-time
        Carbon::setTestNow('2024-11-14 12:13:14');  // Create a specific mocked date

        $userData = [
            'name' => fake()->name,
            'email' =>  fake()->unique()->safeEmail(),
            'password' => fake()->password(8),
            'email_verified_at' => Carbon::now()
        ];

        $expectedTokenData = [
            'access_token' => 'mockedAccessToken',
            'token_type' => 'Bearer',
            'refresh_token' => 'mockedRefreshToken',
            'expires_in' => 3600,
        ];

        // Mocking the OAuthRepository and its getAccessToken method
        $oauthRepositoryMock = Mockery::mock(OAuthRepository::class);
        $oauthRepositoryMock->shouldReceive('getAccessToken')
            ->with($userData, 'password')
            ->once()
            ->andReturns($expectedTokenData);

        // Inject the mock into the service container
        $this->app->instance(OAuthRepository::class, $oauthRepositoryMock);
        // Generate fake user data
        $userRegData = [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'password_confirmation' => $userData['password']
        ];

        // Send a POST request to the register endpoint
        $response = $this->postJson('api/register', $userRegData);

        // Assert the response status is 201 Created
        $response->assertStatus(201);

        // Assert the response contains the success message, token, and user data structure
        $response->assertJsonStructure([
            'success',
            'statusCode',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    // Include other fields as in UserResource
                ],
                'token'
            ]
        ]);

        // Assert the response message is as expected
        $response->assertJson([
            'success' => true,
            'statusCode' => 201,
            'message' => 'User has been registered successfully.'
        ]);

        // Assert the token is not empty
        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_user_registration_fails_with_invalid_data()
    {
        // Create invalid user data
        $userData = [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different'
        ];

        // Send a POST request to the register endpoint
        $response = $this->postJson('api/register', $userData);

        // Assert the response status is 422 Unprocessable Entity (validation error)
        $response->assertStatus(422);

        // Assert the response has validation error messages for each field
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registration_with_missing_fields()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_registration_with_existing_email()
    {
        // Create a user
        $user = User::factory()->create(['email' =>  fake()->unique()->safeEmail()]);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => $user->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_registration_with_invalid_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Close the mockery instance
        parent::tearDown();
    }
}
