<?php

use App\Http\Controllers\Api\v1\AppDataController;
use Illuminate\Support\Facades\Route;

$prefix = env('PREFIX_API') . "/app-data";

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
    Route::get(null, [AppDataController::class, 'all']);
    Route::post('/get-by-key', [AppDataController::class, 'getByKey']);
});
