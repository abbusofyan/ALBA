<?php

use App\Http\Controllers\Api\v1\NotificationController;
use Illuminate\Support\Facades\Route;

$prefix = env('PREFIX_API') . "/notifications";

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
    Route::get('/', [NotificationController::class, 'all']);
	Route::get('/mark-as-read/{notificationId}', [NotificationController::class, 'markAsRead']);
    Route::get('/mark-as-read-all', [NotificationController::class, 'markAsReadAll']);
});
