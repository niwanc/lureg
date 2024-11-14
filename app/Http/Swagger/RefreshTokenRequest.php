<?php

namespace App\Http\Swagger;
/**
 * @OA\Schema(
 *     schema="RefreshTokenRequest",
 *     type="object",
 *     title="Refresh Token Request",
 *     description="Request body for refreshing an access token",
 *     required={"refresh_token"},
 *     @OA\Property(
 *         property="refresh_token",
 *         type="string",
 *         description="The refresh token issued to the user"
 *     )
 * )
 */
class RefreshTokenRequest
{
    // This class is used only for OpenAPI documentation purposes
}
