<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\EventTypeController;

$prefix = env('PREFIX_API', 'v1') . "/event-types";

Route::group(['prefix' => $prefix], function () use ($prefix) {
	Route::get('/', [EventTypeController::class, 'get']);
});
