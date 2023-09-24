<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Auth\AuthController;

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
Route::middleware(['throttle:10,1', 'auth:sanctum'])
    ->prefix('v1')
    ->name('api.')
    ->group(function () {
        Route::apiResource('products', ProductController::class);///->only(['index', 'show']);
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
