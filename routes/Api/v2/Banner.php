<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v2\BannerController;

$prefix = "v2/banners";

Route::group(['prefix' => $prefix], function () use ($prefix) {
	Route::get('/', [BannerController::class, 'get']);
});
