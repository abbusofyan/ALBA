<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

$prefix = 'users';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
	Route::get($prefix . '/export', [UserController::class, 'export'])->name('users.export');
    Route::resource($prefix, UserController::class);

    Route::put('password-update/{id}', [UserController::class, 'updatePassword'])->name('users.password-update');
    Route::get('profile', [UserController::class, 'profile'])->name('user.profile');
    Route::get('profile-edit', [UserController::class, 'profileEdit'])->name('user.profile-edit');
	Route::post('users/{user}/toggleStatus', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
	Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
	Route::get($prefix . '/{user}/recyclings', [UserController::class, 'recyclings'])->name($prefix . '.recyclings');
	Route::get($prefix . '/{user}/vouchers', [UserController::class, 'vouchers'])->name($prefix . '.vouchers');
	Route::post($prefix . '/{user}/ban', [UserController::class, 'ban'])->name($prefix . '.ban');
});
