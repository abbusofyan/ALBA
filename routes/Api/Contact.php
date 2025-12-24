<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ContactController;

$prefix = env('PREFIX_API', 'v1') . "/contact";

Route::group(['prefix' => $prefix], function () use ($prefix) {
	Route::post('/send', [ContactController::class, 'send']);
});
