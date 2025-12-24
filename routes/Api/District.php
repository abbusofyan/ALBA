<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\DistrictController;

$prefix = env('PREFIX_API', 'v1') . "/districts";

Route::group(['prefix' => $prefix], function () use ($prefix) {
	Route::get('/', [DistrictController::class, 'get']);
});
