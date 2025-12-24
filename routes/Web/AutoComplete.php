<?php

use App\Http\Controllers\AutoCompleteController;
use Illuminate\Support\Facades\Route;

$prefix = 'auto-complete';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
	Route::get($prefix . '/user', [AutoCompleteController::class, 'user']);
	Route::get($prefix . '/district', [AutoCompleteController::class, 'district']);
	Route::get($prefix . '/bin', [AutoCompleteController::class, 'bin']);
	Route::get($prefix . '/bin-type', [AutoCompleteController::class, 'binType']);
	Route::get($prefix . '/privateAndAlbaEvent', [AutoCompleteController::class, 'privateAndAlbaEvent']);
});
