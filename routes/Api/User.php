<?php

use Illuminate\Support\Facades\Route;

$prefix = env('PREFIX_API', 'v1') . "/user";

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
    Route::delete('{id}', [\App\Http\Controllers\Api\v1\UserController::class, 'destroy']);
	Route::get('/getAll', [App\Http\Controllers\Api\v1\UserController::class, 'getAll']);
});
