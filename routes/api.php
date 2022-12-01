<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\FacebookController;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Token;

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

//headers : 
    // Accept:application/json
    // Authorization:'Bearer '+ Token this for protected routes
//body:
    // each route has its own required body


//Public routes
Route::post('/login', [AuthController::class, 'login']);
// email:
// password:

Route::post('/register', [AuthController::class, 'store']);
// name:
// role:
// email:
// gender:
// password:
// password_confirmation:

Route::post('/forgotpassword', [AuthController::class, 'forgotpassword']);
// email:

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
    //for create new chanel for notifications

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index');
        //dashboard for news and statisctics
    });

    Route::prefix('auth')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);

        Route::get('profile/{id}', [AuthController::class, 'show']);
        //get the user data

        Route::put('profile/{id}/update', [AuthController::class, 'update']);
        //modify user data
        //the data hear has to be form data
        // name:
        // gender:
        // password:
        // password_confirmation:
        // profile_picture: name of the picture
        // file: containing the binary picture

    });

    Route::middleware('permission:admin')->prefix('admin')->group(function () {
        Route::get('users/{id?}', [AdminController::class, 'users']);
        //get users or secific user
        Route::get('teachers/{id?}', [AdminController::class, 'teachers']);
        //get teachers or secific teacher
        Route::get('students/{id?}', [AdminController::class, 'students']);
        //get students or secific student

        Route::post('users/create', [AdminController::class, 'store']);
        //create new user
        // name:
        // role:
        // email:
        // gender:

        Route::delete('users/{id}/delete', [AdminController::class, 'destroy']);
        //delete user(id)


        Route::put('users/update', [AdminController::class, 'update']);
        //modify user
        // name:
        // role:
        // email:
        // gender:

        Route::get('archive', [AdminController::class, 'archive']);//not now

        Route::get('sessions/{id?}', [AdminController::class, 'sessions']);

        Route::post('sessions/create', [AdminController::class, 'create_session']);
        //classrom:
        //starts_at
        //ends_at:
        //teacher_id: the host of the session
        //studetns: contains all ids for students that belongs to this session

        Route::delete('sessions/{id}/delete', [AdminController::class, 'delete_session']);

        Route::put('sessions/{id}/update', [AdminController::class, 'update_session']);
        //classrom:
        //starts_at:
        //ends_at:
        //teacher_id: the host of the session:
        //studetns: contains all ids for students that belongs to this session:
    });

    Route::middleware('permission:teacher')->prefix('teacher')->group(function () {
        Route::get('sessions/{id}', [TeacherController::class, 'sessions']);
        //id: represent the teacher 

        Route::get('session/{id}', [TeacherController::class, 'show']);
        //id: represent the session

        Route::put('sessions/{id}/reject', [AdminController::class, 'reject_session']);
        //explanation: the reason why reject the session

        Route::put('sessions/{id}/approve', [AdminController::class, 'approve_session']);
    });

    Route::middleware('permission:supervisor')->prefix('parent')->group(function () {
        Route::get('sessions/{id}', [ParentController::class, 'sessions']);
         //id: represent the parent 

        Route::get('session/{id}', [ParentController::class, 'show']);
        //id: represent the session
    });

    Route::middleware('permission:student')->prefix('student')->group(function () {
        Route::get('sessions/{id}', [ClientController::class, 'sessions']);
        //id: represent the student 

        Route::get('session/{id}', [ClientController::class, 'show']);
         //id: represent the session
    });
});
