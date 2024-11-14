<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Repositories\OAuthRepository;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public function __construct(
        protected UserService     $userService,
        protected OAuthRepository $oauthRepository
    )
    {
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User has been registered successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $userData = $request->validated();

        if($userData) {
            $userData['email_verified_at'] = now();
            $user = $this->userService->create($userData);

            // Call the repository method to get the access token
            $tokenData = $this->oauthRepository->getAccessToken($userData, 'password');

            if ($tokenData) {

                $user['token'] = $tokenData;
                return response()->json([
                    'success' => true,
                    'statusCode' => 201,
                    'message' => 'User has been registered successfully.',
                    'data' => [
                        'user' => UserResource::make($user),
                        'token' => $tokenData,
                    ]
                ], 201);
            }
        }


        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login a user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User has been logged successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            // Call the repository method to get the access token
            $tokenData = $this->oauthRepository->getAccessToken([
                'email' => $request->email,
                'password' => $request->password,
            ], 'password');
            if ($tokenData && $user) {
                $user['token'] = $tokenData;
                return response()->json([
                    'success' => true,
                    'statusCode' => 200,
                    'message' => 'User has been logged successfully.',
                    'data' => [
                        'user' => UserResource::make($user),
                        'token' => $tokenData,
                    ]
                ], 200);
            }
        } else {
            return response()->json([
                'success' => true,
                'statusCode' => 401,
                'message' => 'Unauthorized.',
                'errors' => 'Unauthorized',
            ], 401);
        }

        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * @OA\Get(
     *     path="/user",
     *     summary="Get the authenticated user's information",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated user info",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     security={
     *        {"passport": {}}
     *      }
     * )
     */
    public function user(): JsonResponse
    {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'statusCode' => 200,
            'message' => 'Authenticated user info.',
            'data' => [
                'user' => UserResource::make($user),
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/refresh",
     *     summary="Refresh the access token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RefreshTokenRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Refreshed token",
     *         @OA\JsonContent(ref="#/components/schemas/Token")
     *     )
     * )
     */
    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        $tokenData = $this->oauthRepository->getRefreshToken([
            'refresh_token' => $request->refresh_token,
        ], 'refresh_token');

        if ($tokenData) {
            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Refreshed token.',
                'data' => $tokenData,
            ], 200);
        }
        return response()->json([
            'success' => false,
            'statusCode' => 401,
            'message' => 'Unauthorized.',
            'errors' => 'Unauthorized',
        ], 401);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout the authenticated user",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=204,
     *         description="Logged out successfully"
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        Auth::user()?->tokens()->delete();
        return response()->json([
            'success' => true,
            'statusCode' => 200,
            'message' => 'Logged out successfully.',
        ], 200);
    }
}
