<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ServiceController;

$prefix = env('PREFIX_API', 'v1') . "/service";

Route::group(['prefix' => $prefix], function () use ($prefix) {
	Route::get('/get-options', [ServiceController::class, 'getOptions']);
	Route::post('/submit', [ServiceController::class, 'submit']);
});
