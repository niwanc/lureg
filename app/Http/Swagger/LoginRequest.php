<?php

namespace App\Http\Swagger;
/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     title="Login Request",
 *     description="Request body for user login",
 *     required={"email", "password"},
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="The email address of the user"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="The password for the user"
 *     )
 * )
 */
class LoginRequest
{
// This class is used only for OpenAPI documentation purposes
}
