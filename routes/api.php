<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartementController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\PlanController;
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

Route::middleware(['auth:api'])->group(function () {
    Route::post('/email/verify', [AuthController::class, 'verify']);
    // email:
    // code:
    //response :
    // message 
    Route::get('/email/verify', [AuthController::class, 'verify_by_link']);
    // id:
    // code:
    //response :
    // message 


    Route::post('/email/resent/code', [AuthController::class, 'resent_verification']);
    // email:
    //response :
    // message   
});




//email verification required
Route::middleware(['auth:api', 'verified'])->group(function () {

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
        //response :
        // name,email,gender,profile_picture,profile_picture_src,role  

        Route::post('profile/{id}/update', [AuthController::class, 'update']);
        //modify user data
        //the data heer has to be form data
        // name:
        // gender:
        // phone_number
        // password:
        // password_confirmation:
        // file: containing the binary picture
        //response :
        // name,email,gender,profile_picture,profile_picture_src,role 

    });

    /*
     *
     * for each permission you have to sepcify the role in params
     *
    */

    Route::middleware('permission:admin')->prefix('admin')->group(function () {

        Route::get('branchs/{id?}', [DepartementController::class, 'branchs']);
        //response :
        // name

        Route::post('branchs/create', [DepartementController::class, 'create_branch']);
        //name
        //response :
        // ok 

        Route::delete('branchs/{id}/delete', [DepartementController::class, 'delete_branch']);
        //response :
        // ok

        Route::put('branchs/{id}/update', [DepartementController::class, 'update_branch']);
        //name
        //response :
        // ok 

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
        Route::get('parents/{id?}', [AdminController::class, 'parents']);
        //get parent or secific parent
        //response :
        // id,name,email,gender,role 

        Route::post('users/create', [AdminController::class, 'store']);
        //create new user
        // name:
        // user_role:
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
        // user_role:
        // phone_number
        // email:
        // gender:
        //response :
        // ok

        Route::get('archive', [AdminController::class, 'archive']); //not now

        Route::get('sessions/{id?}', [AdminController::class, 'sessions']);
        //response :
        // starts_at,ends_at,classroom,teacher,group

        Route::post('sessions/create', [AdminController::class, 'create_session']);
        //classrom:
        //starts_at
        //ends_at:
        //teacher_id: the host of the session
        //group_id: group's id:
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
        //group_id: group's id:
        //response :
        // ok 

        Route::get('palns/{id?}', [PlanController::class, 'index']);
        //response :
        // price,duration,benefits,clients

        Route::post('plans/create', [PlanController::class, 'create']);
        //price:
        //duration
        //benefits:
        //departement_id:id of the departement
        //teacher_id:id of the teacher

        //response :
        // ok

        Route::delete('plans/{id}/delete', [PlanController::class, 'delete']);
        //response :
        // ok

        Route::put('plans/{id}/update', [PlanController::class, 'update']);
        //price:
        //duration
        //benefits:
        //departement_id:id of the departement
        //teacher_id:id of the teacher

        //response :
        // ok 

        Route::get('groups/{id?}', [AdminController::class, 'groups']);
        //response :
        // name,members,capacity,teacher,departement

        Route::post('groups/create', [AdminController::class, 'create_group']);
        //name:
        //capacity
        //departement_id:id of the departement
        //teacher_id:id of the teacher

        //response :
        // ok

        Route::delete('groups/{id}/delete', [AdminController::class, 'delete_group']);
        //response :
        // ok

        Route::put('groups/{id}/update', [AdminController::class, 'update_group']);
        //name:
        //capacity:
        //departement_id:id of the departement
        //teacher_id:id of the teacher


        //response :
        // ok 
        Route::post('groups/{id}/students', [AdminController::class, 'group_student']);

        //students:ids of the students

        //response :
        // ok 


        Route::get('departements/{id?}', [DepartementController::class, 'index']);
        //response :
        // branch,education,sepciality,year

        Route::post('departements/create', [DepartementController::class, 'create']);
        //education:
        //sepeciality
        //year:
        //branch_id: branch
        //response :
        // ok 

        Route::delete('departements/{id}/delete', [DepartementController::class, 'delete']);
        //response :
        // ok

        Route::put('departements/{id}/update', [DepartementController::class, 'update']);
        //education:
        //sepeciality
        //year:
        //branch_id: branch
        //response :
        // ok 
    });

    Route::middleware('permission:teacher')->prefix('teacher')->group(function () {

        Route::get('sessions/{id}', [TeacherController::class, 'sessions']);
        //id: represent the teacher 
        //response :
        // starts_at,ends_at,classroom,teacher,group

        Route::get('session/{id}', [TeacherController::class, 'show']);
        //id: represent the session
        //response :
        // starts_at,ends_at,classroom,teacher,group

        Route::put('sessions/{id}/reject', [TeacherController::class, 'reject_session']);
        //explanation: the reason why reject the session
        //response :
        // ok

        Route::put('sessions/{id}/approve', [TeacherController::class, 'approve_session']);
        //response :
        // ok
    });
    Route::middleware('permission:supervisor')->prefix('parent')->group(function () {
        Route::post('clients/choose/plan', [PlanController::class, 'choose_plan']);
        // client_id:
        // plan_id:
        //response :
        // ok
    });


    Route::middleware('permission:supervisor')->prefix('parent')->group(function () {

        Route::get('sessions/{id?}', [ParentController::class, 'sessions']);
        //id: represent the parent 
        //response :
        // starts_at,ends_at,classroom,teacher,group


        Route::get('students/{id?}', [ParentController::class, 'students']);
        //get students or secific student that belongs to the parent
        //response :
        // id,name,email,gender,role 

        Route::post('students/create', [ParentController::class, 'add_student']);
        //create new student
        // name:
        // phone_number:
        // email:
        // gender:
        //response :
        // ok 

        Route::put('students/{id}/update', [ParentController::class, 'update_student']);
        //update new student
        // name:
        // phone_number:
        // email:
        // gender:
        //response :
        // ok

        Route::delete('students/{id}/delete', [ParentController::class, 'delete_student']);
        //response :
        // ok 
    });

    Route::middleware('permission:student')->prefix('student')->group(function () {

        Route::get('sessions/{id?}', [StudentController::class, 'sessions']);
        //response :
        // starts_at,ends_at,classroom,teacher,group

    });
});
