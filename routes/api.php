<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AuthController;

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

Route::prefix('pppix/api')->group(function () {
    Route::get('/testeuser', [DataController::class, 'testeuser']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('car', CarController::class);
    Route::get('data/home', [DataController::class, 'home']);

    Route::prefix('group')->group(function () {
        Route::post('/users', [GroupController::class, 'validUsers']);
        Route::post('/remove', [GroupController::class, 'remove']);
        Route::get('/', [GroupController::class, 'index']);
        Route::post('/exit', [GroupController::class, 'exit']);
        Route::post('/update', [GroupController::class, 'update']);
    });

    Route::prefix('alert')->group(function () {
        Route::get('/', [AlertController::class, 'index']);
        Route::get('/wait', [AlertController::class, 'wait']);
        Route::post('/create', [AlertController::class, 'create']);
        Route::post('/finish', [AlertController::class, 'finish']);
        Route::post('/finish/sender', [AlertController::class, 'finishAllSends']);
        Route::post('/finish/all', [AlertController::class, 'finishAll']);
        Route::post('/stop', [AlertController::class, 'stopAlert']);
        Route::post('/update/token', [AlertController::class, 'updateFcmToken']);
    });

    Route::prefix('sms')->group(function () {
        Route::post('/invite', [SmsController::class, 'invite']);
    });

    Route::prefix('user')->group(function () {
        Route::post('/change/passwords', [UserController::class, 'changePasswords']);
        Route::post('/change/car', [UserController::class, 'changeCar']);
    });

    Route::post('/position/update', [PositionController::class, 'update']);
});

Route::prefix('auth')->group(function () {
    Route::post('/recover/generate', [AuthController::class, 'sendEmailRecover']);
    Route::post('/recover/check', [AuthController::class, 'checkCode']);
    Route::post('/recover/change', [AuthController::class, 'changePassword']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/signin', [AuthController::class, 'signin']);
    Route::get('/check', [AuthController::class, 'check'])->middleware('auth:sanctum');
});
