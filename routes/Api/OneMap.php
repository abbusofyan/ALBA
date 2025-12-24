<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\OneMapController;

$prefix = env('PREFIX_API', 'v1') . "/onemap";

Route::group(['prefix' => $prefix], function () use ($prefix) {
	Route::get('/getLocationByPostalCode/{postal_code}', [OneMapController::class, 'getLocationByPostalCode']);
});
