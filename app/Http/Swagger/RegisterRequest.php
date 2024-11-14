<?php

namespace App\Http\Swagger;

/**
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     title="Register Request",
 *     description="Request body for user registration",
 *     required={"name", "email", "password"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the user"
 *     ),
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
 *     ),
 *          @OA\Property(
 *          property="password_confirmation",
 *          type="string",
 *          format="password",
 *          description="The password confirmation for the user"
 *      )
 * )
 */
class RegisterRequest
{
// This class is used only for OpenAPI documentation purposes
}
