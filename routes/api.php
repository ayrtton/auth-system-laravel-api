<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VerificationController;
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

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::get('verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('resend-verification-mail', [VerificationController::class, 'resend'])->name('verification.resend');

    Route::post('password-reset-mail', [PasswordResetController::class, 'sendPasswordResetMail']);
    Route::post('password-reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['middleware' => 'verified'], function() {
            Route::post('me', [AuthController::class, 'me']);
        });

        Route::get('users', [UserController::class, 'index']);
    
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
