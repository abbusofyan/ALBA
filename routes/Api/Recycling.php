<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\RecyclingController;

$prefix = env('PREFIX_API', 'v1') . "/recyclings";

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => $prefix], function () use ($prefix) {
	Route::post('/submit', [RecyclingController::class, 'submit']);
	Route::get('/getAll', [RecyclingController::class, 'getAll']);
});
