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

//CHECK TOKEN
Route::post('/check-token', [AuthController::class, 'checkToken']);

//ROUTES
Route::group(['middleware' => ['auth:sanctum']], function () {

    //USERS
    Route::resource('/users', UsersController::class);

    //LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);

    //SCHEDULES
    Route::resource('/schedules', SchedulesController::class);

    //PACKAGES
    Route::resource('/packages', PackagesController::class);

    //PEOPLE
    Route::get('/people/{type}', [PeopleController::class, 'show']);

    //CLASS BLOCK
    Route::resource('/class-block', ClassBlockController::class);

    //NOTIFICATIONS
    Route::resource('/notifications', NotificationsController::class);

    //REPORTS
    Route::get('/packagesByStudent/{id}', [ReportsController::class, 'packagesByStudent']);
});
