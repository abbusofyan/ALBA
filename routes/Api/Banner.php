<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\BannerController;

$prefix = env('PREFIX_API', 'v1') . "/banners";

Route::group(['prefix' => $prefix], function () use ($prefix) {
	Route::get('/', [BannerController::class, 'get']);
});
