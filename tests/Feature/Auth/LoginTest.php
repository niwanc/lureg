<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Repositories\OAuthRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Mockery;
use Tests\TestCase;

class LoginTest extends TestCase
{

    use DatabaseTransactions;

    protected User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model $user;
    /**
     * Set up the test environment.
     * Create a user in the database with a hashed password.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Create a user in the database
        $this->user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
    }
    /**
     * Test successful login.
     *
     * @return void
     */
    public function test_successful_login()
    {
        $userData = [
            'email' => $this->user->email,
            'password' => 'password123',
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

        // Send a POST request to the login endpoint with the user credentials
        $response = $this->postJson('/api/login', $userData);

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the response contains the 'data' field with user info and token
        $response->assertJsonStructure([
            'success',
            'statusCode',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'email',
                    'name', // Adjust according to the actual user resource attributes
                ],
                'token',
            ]
        ]);

        // Assert that the token exists and is not empty
        $this->assertNotEmpty($response->json('data.token'));

    }
    /**
     * Test unsuccessful login returns unauthorized.
     *
     * @return void
     */
    public function test_unsuccessful_login_returns_unauthorized()
    {
        // Send a POST request to the login endpoint with incorrect credentials
        $response = $this->postJson('api/login', [
            'email' => 'admin@test123.com',
            'password' => 'incorrectPassword', // Wrong password
        ]);

        // Assert that the response status is 401 Unauthorized
        $response->assertStatus(401);

        // Assert that the response contains the error message
        $response->assertJson([
            'success' => true,
            'statusCode' => 401,
            'message' => 'Unauthorized.',
            'errors' => 'Unauthorized',
        ]);
    }
    /**
     * Test login with non-existent email.
     *
     * @return void
     */
    public function test_login_with_non_existent_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test login with missing fields.
     *
     * @return void
     */
    public function test_login_with_missing_fields()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422);
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Close the mockery instance
        parent::tearDown();
    }
}
