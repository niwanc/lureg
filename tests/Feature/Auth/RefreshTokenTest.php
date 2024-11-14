<?php

namespace Tests\Feature\Auth;

use App\Repositories\OAuthRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    use DatabaseTransactions;
    protected function setUp(): void
    {
        parent::setUp();

        // Mock OAuthRepository for the test
        $this->oauthRepository = Mockery::mock(OAuthRepository::class);
        $this->app->instance(OAuthRepository::class, $this->oauthRepository);
    }

    /**
     * Test successful refresh token generation.
     *
     * @return void
     */
    public function test_refresh_token_success()
    {
        // Mock token data
        $tokenData = [
            'access_token' => 'newAccessToken123',
            'refresh_token' => 'newRefreshToken123',
            'expires_in' => 3600,
        ];

        // Mock the OAuthRepository method
        $this->oauthRepository->shouldReceive('getRefreshToken')
            ->with(['refresh_token' => 'validRefreshToken'], 'refresh_token')
            ->once()
            ->andReturn($tokenData);

        // Make a POST request with a valid refresh token
        $response = $this->postJson('/api/refresh', [
            'refresh_token' => 'validRefreshToken',
        ]);

        // Assert status and structure of the response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Refreshed token.',
                'data' => $tokenData,
            ]);
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Close the mockery instance
        parent::tearDown();
    }
}
