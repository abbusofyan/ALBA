<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('notifications', NotificationController::class);
    Route::post('notifications/{notification}', [NotificationController::class, 'update']);
    Route::post('/notifications/{notification}/push', [NotificationController::class, 'push']);
});
