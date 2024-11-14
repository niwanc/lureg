<?php

namespace Tests\Unit;

use App\Repositories\OAuthRepository;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class OAuthRepositoryTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function GetAccessToken_Returns_CorrectData()
    {
        // Arrange
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Mock GuzzleHttp\Client
        $mockClient = Mockery::mock(Http::class);

        // Mock the response from the OAuth server
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockResponse->shouldReceive('getBody')
            ->andReturn(json_encode([
                'access_token' => 'mockedAccessToken',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], JSON_THROW_ON_ERROR));

        $mockClient->shouldReceive('post')
            ->once()
            ->with('https://oauth-server.com/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'email' => 'test@example.com',
                    'password' => 'password123',
                ]
            ])
            ->andReturn($mockResponse);

        // Inject the mocked client into the OAuthRepository
        $oauthRepository = new OAuthRepository();

        // Act
        $tokenData = $oauthRepository->getAccessToken($userData, 'password');

        // Assert
        $this->assertArrayHasKey('access_token', $tokenData);
        $this->assertEquals('mockedAccessToken', $tokenData['access_token']);
        $this->assertEquals('Bearer', $tokenData['token_type']);
        $this->assertEquals(3600, $tokenData['expires_in']);
    }

    // Don't forget to close Mockery to avoid memory leaks
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
