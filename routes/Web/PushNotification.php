<?php

use App\Http\Controllers\PushNotificationController;
use Illuminate\Support\Facades\Route;

$prefix = 'push-notifications';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
	Route::resource($prefix, PushNotificationController::class);
});
