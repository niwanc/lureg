<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OAuthRepository
{
    public function getAccessToken(array $userData, string $grantType): ?array
    {
        try {
            // Make the HTTP request to get the access token
            $response = Http::asForm()->post(config('app.app_host') . '/oauth/token', [
                'grant_type' => $grantType ?? 'password',
                'client_id' => config('passport.password_grant_access_client.id'),
                'client_secret' => config('passport.password_grant_access_client.secret'),
                'username' => $userData['email'],
                'password' => $userData['password'],
                'scope' => '',
            ]);

            // Check if the response was successful
            if ($response->successful()) {
                return $response->json();  // Return the token details
            }

            $errorMessage = $response->json()['message'] ?? 'Unknown error';
            Log::error('OAuth token generation failed', [
                'status' => $response->status(),
                'response' => $response->json(),
                'error' => $errorMessage
            ]);

            return null;

            // If unsuccessful, handle the error
        } catch (\Exception $e) {
            Log::error('OAuth token generation exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getRefreshToken(array $refreshToken, string $grantType): ?array
    {
        try {
            // Make the HTTP request to get the access token
            $response = Http::asForm()->post(config('app.app_host') . '/oauth/token', [
                'grant_type' => $grantType ?? 'refresh_token',
                'client_id' => config('passport.password_grant_access_client.id'),
                'client_secret' => config('passport.password_grant_access_client.secret'),
                'refresh_token' => $refreshToken['refresh_token'],
                'scope' => '',
            ]);
            // Check if the response was successful
            if ($response->successful()) {
                return $response->json();  // Return the token details
            }

            $errorMessage = $response->json()['message'] ?? 'Unknown error';
            // If unsuccessful, handle the error
            Log::error('OAuth token generation failed', [
                'status' => $response->status(),
                'response' => $response->json(),
                'error' => $errorMessage
            ]);
            return null;
            // If unsuccessful, handle the error
        } catch (\Exception $e) {
            Log::error('OAuth token generation exception', [
                'message' => $e->getMessage()
            ]);

            return null;
        }
    }

}
