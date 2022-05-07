<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassBlockController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

//API VERSION
Route::get('/', function () {
    return 'VERSION ' . env('API_VERSION');
});

//LOGIN
Route::post('/login', [AuthController::class, 'login']);
Route::post('/new_user', [UsersController::class, 'new_user']);
Route::post('/reset_password', [UsersController::class, 'reset_password']);

//CHECK TOKEN
Route::post('/check_token', [AuthController::class, 'checkToken']);

//ROUTES
Route::group(['middleware' => ['auth:sanctum']], function () {

    //USERS
    Route::get('/users/list_teachers', [UsersController::class, 'list_teachers']);
    Route::resource('/users', UsersController::class)->only('index', 'store', 'show', 'update', 'destroy');

    //LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);

    //SCHEDULES
    Route::resource('/schedules', SchedulesController::class)->only('index', 'store', 'show', 'update', 'destroy');
    Route::get('/schedules/next_class/{user_id}', [SchedulesController::class, 'next_class']);
    Route::get('/schedules/last_class/{user_id}', [SchedulesController::class, 'last_class']);
    Route::get('/schedules/by_user/{user_id}', [SchedulesController::class, 'by_user']);
    Route::post('/schedules/cancel', [SchedulesController::class, 'cancel']);
    Route::post('/schedules/cancel_confirm', [SchedulesController::class, 'cancel_confirm']);
    Route::get('/schedules/check_schedule/{teacher}/{date}', [SchedulesController::class, 'check_schedule']);
    Route::get('/schedules/appraisal_by_user/{user_id}', [SchedulesController::class, 'appraisal_by_user']);

    //PACKAGES
    Route::resource('/packages', PackagesController::class)->only('index', 'store', 'show', 'update', 'destroy');
    Route::get('/packages/balance/{user_id}', [PackagesController::class, 'balance']);
    Route::get('/packages/by_user/{user_id}', [PackagesController::class, 'by_user']);

    //PEOPLE
    Route::get('/people/{type}', [PeopleController::class, 'show']);

    //CLASS BLOCK
    Route::resource('/class-block', ClassBlockController::class);

    //NOTIFICATIONS
    Route::resource('/notifications', NotificationsController::class)->only('index', 'store', 'show', 'update', 'destroy');
    Route::get('/notifications/count_by_user/{id}', [NotificationsController::class, 'count_by_user']);
    Route::get('/notifications/by_user/{id}', [NotificationsController::class, 'by_user']);
    Route::post('/notifications/check_read/{id}', [NotificationsController::class, 'check_read']);

    //REPORTS
    Route::get('/packagesByStudent/{id}', [ReportsController::class, 'packagesByStudent']);
});
