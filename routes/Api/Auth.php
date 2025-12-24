<?php

use Illuminate\Support\Facades\Route;

$prefix = env('PREFIX_API', 'v1') . "/auth";

Route::group(['prefix' => $prefix], function () use ($prefix) {
    Route::post('/Login', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'login']);
    Route::middleware(['throttle:resend-code'])->post('/ResendCode', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'resendCode']);
    Route::post('/Verify', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'verify']);
    Route::post('/RefreshToken', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'refresh']);
    Route::get('/Logout', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'logout'])->middleware(\App\Http\Middleware\Sanctum::class);
    Route::post('/Register', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'register']);
    Route::post('/RegisterEntity/{entity}', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'registerEntity']);
    Route::post('/VerifyRegister', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'verifyRegister']);
    Route::post('/ForgotPassword', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'forgotPassword']);
    Route::post('/ResetPassword', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'resetPassword']);
    Route::post('/RequestAccount', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'requestAccount']);
    Route::post('/ValidateProfile', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'validateProfile']);
    Route::post('/ValidateCredential', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'validateCredential']);
    Route::post('/validatePhoneNumber', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'validatePhoneNumber']);
    Route::post('/validateEmail', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'validateEmail']);
    Route::post('/validatePostalCode', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'validatePostalCode']);
    Route::post('/validatePassword', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'validatePassword']);
	Route::post('/validateNickname', [\App\Http\Controllers\Api\v1\AuthenticationController::class, 'validateNickname']);
});
