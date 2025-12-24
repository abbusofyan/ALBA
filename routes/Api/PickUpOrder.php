<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\PickUpOrderController;

$prefix = env('PREFIX_API', 'v1') . "/pick-up-orders";

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
	Route::post('/submit', [PickUpOrderController::class, 'submit']);
	Route::get('/get-weight-range', [PickUpOrderController::class, 'getWeightRange']);
	Route::get('/get-time-slot', [PickUpOrderController::class, 'getTimeSlot']);
	Route::get('/get-waste-category', [PickUpOrderController::class, 'getWasteCategory']);
});
