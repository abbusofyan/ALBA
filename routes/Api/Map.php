<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\MapController;

$prefix = env('PREFIX_API', 'v1') . "/map";

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
	Route::get('/getNearbyBinLocations', [MapController::class, 'getNearbyBinLocations']);
});
