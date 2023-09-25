<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Public routes for registration and login of users.
 */
Route::post('/v1/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/v1/login', [AuthController::class, 'login'])->name('api.login');

/**
 * Protected routes.
 * Limiting sending requests to the endpoints.
 * Use authentication to protect routes (Sanctum is already preinstalled in the fresh Laravel installation).
 */
Route::middleware(['throttle:10,1', 'auth:sanctum']) // middlewares for throttling and authentication
    ->prefix('v1') // API version
    ->name('api.') // route name prefix
    ->group(function () {
        // TODO: extract 'index' and 'show' method outside of protected routes if they should be public.
        Route::apiResource('products', ProductController::class)
            ->missing(function (Request $request): JsonResponse {
                return response()->json([
                    'message' => 'Product not found',
                ], Response::HTTP_NOT_FOUND);
            });

        // Logout route.
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
