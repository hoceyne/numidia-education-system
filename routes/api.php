<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\FacebookController;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'store']);
Route::post('/forgotpassword', [AuthController::class, 'forgotpassword']);
//social authentification
Route::controller(GoogleController::class)->group(function () {
    Route::get('/auth/google', 'redirectToGoogle');
    Route::get('/auth/google/callback', 'handleGoogleCallback');
});

Route::controller(FacebookController::class)->group(function () {
    Route::get('/auth/facebook', 'redirectToFacebook');
    Route::get('/auth/facebook/callback', 'handleFacebookCallback');
});

//Protected routes
Route::middleware('auth:api')->group(function () {

    WebSocketsRouter::webSocket('/my-websocket', \App\CustomWebSocketHandler::class);

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index');
    });

    Route::prefix('auth')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('profile/{id}', [AuthController::class, 'show']);
        Route::put('profile/{id}/update', [AuthController::class, 'change_password']);
    });

    Route::middleware('permission:admin')->prefix('admin')->group(function () {
        Route::get('users/{id?}', [AdminController::class, 'users']);
        Route::post('users/create', [AdminController::class, 'store']);
        Route::delete('users/{id}/delete', [AdminController::class, 'destroy']);
        Route::put('users/update', [AdminController::class, 'update']);
        Route::get('archive', [AdminController::class, 'archive']);
        Route::get('sessions/{id?}', [AdminController::class, 'sessions']);
        Route::post('sessions/create', [AdminController::class, 'create_session']);
        Route::delete('sessions/{id}/delete', [AdminController::class, 'delete_session']);
        Route::put('sessions/{id}/update', [AdminController::class, 'update_session']);
    });

    Route::middleware('permission:teacher')->prefix('teacher')->group(function () {
        Route::get('sessions/{id}', [AdminController::class, 'sessions']);
        Route::get('session/{id}', [AdminController::class, 'show']);
        Route::put('sessions/{id}/reject', [AdminController::class, 'reject_session']);
        Route::put('sessions/{id}/approve', [AdminController::class, 'approve_session']);
    });

    Route::middleware('permission:supervisor')->prefix('parent')->group(function () {
        Route::get('sessions/{id}', [AdminController::class, 'sessions']);
        Route::get('session/{id}', [AdminController::class, 'show']);
    });

    Route::middleware('permission:student')->prefix('student')->group(function () {
        Route::get('sessions/{id}', [AdminController::class, 'sessions']);
        Route::get('session/{id}', [AdminController::class, 'show']);
    });
});
