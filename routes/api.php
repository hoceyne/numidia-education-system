<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\TeacherController;
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




//headers : 
//'Content-type' : 'application/json',
// Accept:application/json
// Authorization:'Bearer '+ Token this for protected routes
//body:
// each route has its own required body



//Public routes
Route::post('/login', [AuthController::class, 'login']);
// email:
// password:
//response :
    // token,role,id

Route::post('/register', [AuthController::class, 'store']);
// name:
// role:
// email:
// gender:
// phone_number
// password:
// password_confirmation:
//response :
    // token,role,id

Route::post('/forgotpassword', [AuthController::class, 'forgotpassword']);
// email:
//response :
    // ok

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
    
    Route::post('/email/verify', [AuthController::class, 'verify']);
        // email:
        // code:
        //response :
            // message  

    Route::prefix('auth')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);

        Route::get('profile/{id}', [AuthController::class, 'show'])->middleware('verified');
        //get the user data
        //response :
            // name,email,gender,profile_picture,profile_picture_src,role  

        Route::post('profile/{id}/update', [AuthController::class, 'update'])->middleware('verified');
        //modify user data
        //the data heer has to be form data
        // name:
        // gender:
        // phone_number
        // password:
        // password_confirmation:
        // profile_picture: name of the picture
        // file: containing the binary picture
        //response :
            // name,email,gender,profile_picture,profile_picture_src,role 

    });
    //for each permission tou had to sepcify the role

    Route::middleware('permission:admin')->prefix('admin')->group(function () {
        Route::get('users/{id?}', [AdminController::class, 'users']);
        //get users or secific user
        //response :
            // id,name,email,gender,role 

        Route::get('teachers/{id?}', [AdminController::class, 'teachers']);
        //get teachers or secific teacher
        //response :
            // id,name,email,gender,role 
        Route::get('students/{id?}', [AdminController::class, 'students']);
        //get students or secific student
        //response :
            // id,name,email,gender,role 

        Route::post('users/create', [AdminController::class, 'store']);
        //create new user
        // name:
        // role:
        // phone_number
        // email:
        // gender:
        //response :
            // ok 

        Route::delete('users/{id}/delete', [AdminController::class, 'destroy']);
        //delete user(id)
        //response :
            // ok


        Route::put('users/update', [AdminController::class, 'update']);
        //modify user
        // name:
        // role:
        // phone_number
        // email:
        // gender:
        //response :
            // ok

        Route::get('archive', [AdminController::class, 'archive']); //not now

        Route::get('sessions/{id?}', [AdminController::class, 'sessions']);
        //response :
            // starts_at,ends_at,classroom,teacher,students:[list of students]

        Route::post('sessions/create', [AdminController::class, 'create_session']);
        //classrom:
        //starts_at
        //ends_at:
        //teacher_id: the host of the session
        //studetns: contains all ids for students that belongs to this session
        //response :
            // ok

        Route::delete('sessions/{id}/delete', [AdminController::class, 'delete_session']);
        //response :
            // ok

        Route::put('sessions/{id}/update', [AdminController::class, 'update_session']);
        //classrom:
        //starts_at:
        //ends_at:
        //teacher_id: the host of the session:
        //studetns: contains all ids for students that belongs to this session:
        //response :
            // ok 
    });

    Route::middleware('permission:teacher')->prefix('teacher')->group(function () {
        Route::get('sessions/{id}', [TeacherController::class, 'sessions']);
        //id: represent the teacher 
        //response :
            // starts_at,ends_at,classroom,teacher,students:[list of students]

        Route::get('session/{id}', [TeacherController::class, 'show']);
        //id: represent the session
        //response :
            // starts_at,ends_at,classroom,teacher,students:[list of students]

        Route::put('sessions/{id}/reject', [AdminController::class, 'reject_session']);
        //explanation: the reason why reject the session
        //response :
            // ok

        Route::put('sessions/{id}/approve', [AdminController::class, 'approve_session']);
        //response :
            // ok
    });

    Route::middleware('permission:supervisor')->prefix('parent')->group(function () {
        Route::get('sessions/{id}', [ParentController::class, 'sessions']);
        //id: represent the parent 
        //response :
            // starts_at,ends_at,classroom,teacher,students:[list of students]

        Route::get('session/{id}', [ParentController::class, 'show']);
        //id: represent the session
        //response :
            // starts_at,ends_at,classroom,teacher,students:[list of students]
    });

    Route::middleware('permission:student')->prefix('student')->group(function () {
        Route::get('sessions/{id}', [StudentController::class, 'sessions']);
        //id: represent the student 
        //response :
            // starts_at,ends_at,classroom,teacher,students:[list of students]

        Route::get('session/{id}', [StudentController::class, 'show']);
        //id: represent the session
        //response :
            // starts_at,ends_at,classroom,teacher,students:[list of students]
    });
});
