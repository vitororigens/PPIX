<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\SmsController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('car', '\App\Http\Controllers\CarController');
    Route::get('data/home', '\App\Http\Controllers\DataController@home');
    Route::prefix('group')->controller(\App\Http\Controllers\GroupController::class)->group(function () {
        Route::post('/users', 'validUsers');
        Route::post('/remove', 'remove');
        Route::get('/', 'index');
        Route::post('/exit', 'exit');
        Route::post('/update', 'update');
    });

    Route::prefix('alert')->controller(\App\Http\Controllers\AlertController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/wait', 'wait');
        Route::post('/create', 'create');
        Route::post('/finish', 'finish');
        Route::post('/finish/all', 'finishAll');
        Route::post('/stop', 'stopAlert');
        Route::post('/update/token', 'updateFcmToken');
        
    });

    Route::prefix('sms')->controller(\App\Http\Controllers\SmsController::class)->group(function () {
        Route::post('/invite', 'invite');
    });

    Route::prefix('user')->controller(\App\Http\Controllers\UserController::class)->group(function () {
        Route::post('/change/passwords', 'changePasswords');
        Route::post('/change/car', 'changeCar');
    });


    Route::post('/position/update', '\App\Http\Controllers\PositionController@update');
    
});

Route::prefix('auth')->controller(\App\Http\Controllers\AuthController::class)->group(function () {
    Route::post('/recover/generate', 'sendEmailRecover');
    Route::post('/recover/check', 'checkCode');
    Route::post('/recover/change', 'changePassword');
    Route::post('/register', 'register');
    Route::post('/signin', 'signin');
    Route::get('/check', 'check')->middleware('auth:sanctum');
});
