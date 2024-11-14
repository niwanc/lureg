<?php

namespace App\Http\Swagger;

/**
 * @OA\Schema(
 *     schema="Token",
 *     type="object",
 *     title="Token",
 *     description="Response body for access token",
 *     required={"access_token", "token_type", "expires_in"},
 *     @OA\Property(
 *         property="access_token",
 *         type="string",
 *         description="The access token issued to the user"
 *     ),
 *     @OA\Property(
 *         property="token_type",
 *         type="string",
 *         description="The type of the token issued"
 *     ),
 *     @OA\Property(
 *         property="expires_in",
 *         type="integer",
 *         description="The duration in seconds for which the token is valid"
 *     ),
 *     @OA\Property(
 *         property="refresh_token",
 *         type="string",
 *         description="The refresh token issued to the user"
 *     )
 * )
 */
class Token
{
// This class is used only for OpenAPI documentation purposes
}
