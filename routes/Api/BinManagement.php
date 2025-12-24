<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\BinManagementController;

$prefix = env('PREFIX_API', 'v1') . "/bin-management";

Route::post($prefix . '/getAllBinIds', [BinManagementController::class, 'getAllBinIds']);
Route::post($prefix . '/getAll', [BinManagementController::class, 'getAll']);
Route::get($prefix . '/get/{binId}', [BinManagementController::class, 'get']);

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
	Route::get('/pingEnvipco', [BinManagementController::class, 'pingEnvipco']);
	Route::get('/scan', [BinManagementController::class, 'scan']);
});
