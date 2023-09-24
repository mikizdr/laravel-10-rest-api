<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Api\LoginRequest;
use App\Http\Requests\Auth\Api\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for registering, logging and logout users using Sanctum
 */
class AuthController extends Controller
{
    /**
     * Validates the request that contains name, email and password in the body.
     * If validation passes successfully, a new user has been created together
     * with the authentication token.
     *
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     *
     * @throws \Throwable
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $validated = array_merge($request->validated(), [
                'password' => Hash::make($request->password),
            ]);

            $user = User::create($validated);

            return response()->json([
                'message' => __('Successfully created user'),
                'user' => $user,
                'token' => $this->generateToken($user),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __($th->getMessage()),
                'token' => null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logins a user with the given credentials if they are valid ones.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     *
     * @throws \Throwable
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->validated('password'), $user->password)) {
                return response()->json([
                    'message' => __('Wrong credentials were provided'),
                    'token' => null,
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'message' => __('Successfully logged in user'),
                'user' => $user,
                'token' => $this->generateToken($user),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __($th->getMessage()),
                'token' => null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logout a current user.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => __('User logged out'),
            'token' => null,
        ]);
    }

    /**
     * Generates a token for the given user.
     *
     * @param User $user
     *
     * @return string
     */
    private function generateToken(User $user): string
    {
        return $user->createToken('API_TOKEN')->plainTextToken;
    }
}
