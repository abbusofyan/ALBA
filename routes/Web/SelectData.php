<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

$prefix = 'data';

Route::middleware('auth')->group(function () use ($prefix) {
    Route::prefix($prefix)->group(function () {
        Route::post('/users', [UserController::class, 'selectQuery']);
    });
});
