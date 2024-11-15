<?php

namespace Tests\Unit;

use App\Repositories\OAuthRepository;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase ;
class OAuthRepositoryTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    private OAuthRepository $oauthRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->oauthRepository = new OAuthRepository();
    }

    public function test_GetAccessToken_Successful()
    {
        // Arrange: Fake HTTP response for a successful token request
        Http::fake([
            config('app.app_host') . '/oauth/token' => Http::response([
                'access_token' => 'test_access_token',
                'refresh_token' => 'test_refresh_token',
                'expires_in' => 3600,
            ], 200),
        ]);

        $userData = ['email' => 'test@example.com', 'password' => 'password'];

        // Act: Call the getAccessToken method
        $response = $this->oauthRepository->getAccessToken($userData, 'password');

        // Assert
        $this->assertIsArray($response);
        $this->assertEquals('test_access_token', $response['access_token']);
        $this->assertEquals('test_refresh_token', $response['refresh_token']);
        $this->assertEquals(3600, $response['expires_in']);
    }

    public function test_GetAccessToken_Unsuccessful()
    {
        // Arrange: Fake HTTP response for a failed token request
        Http::fake([
            config('app.app_host') . '/oauth/token' => Http::response([], 401),
        ]);

        $userData = ['email' => 'test@example.com', 'password' => 'wrong_password'];

        // Act: Call the getAccessToken method
        $response = $this->oauthRepository->getAccessToken($userData, 'password');

        // Assert
        $this->assertNull($response);
    }

    public function test_GetRefreshToken_Successful()
    {
        // Arrange: Fake HTTP response for a successful refresh token request
        Http::fake([
            config('app.app_host') . '/oauth/token' => Http::response([
                'access_token' => 'new_access_token',
                'refresh_token' => 'new_refresh_token',
                'expires_in' => 3600,
            ], 200),
        ]);

        $refreshToken = ['refresh_token' => 'test_refresh_token'];

        // Act: Call the getRefreshToken method
        $response = $this->oauthRepository->getRefreshToken($refreshToken, 'refresh_token');

        // Assert
        $this->assertIsArray($response);
        $this->assertEquals('new_access_token', $response['access_token']);
        $this->assertEquals('new_refresh_token', $response['refresh_token']);
        $this->assertEquals(3600, $response['expires_in']);
    }

    public function test_GetRefresh_Token_Unsuccessful()
    {
        // Arrange: Fake HTTP response for a failed refresh token request
        Http::fake([
            config('app.app_host') . '/oauth/token' => Http::response([], 401),
        ]);

        $refreshToken = ['refresh_token' => 'invalid_refresh_token'];

        // Act: Call the getRefreshToken method
        $response = $this->oauthRepository->getRefreshToken($refreshToken, 'refresh_token');

        // Assert
        $this->assertNull($response);
    }

    // Don't forget to close Mockery to avoid memory leaks
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
