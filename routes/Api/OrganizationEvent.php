<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\OrganizationEventController;

$prefix = env('PREFIX_API', 'v1') . "/organization-events";

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
	Route::get('/upcoming', [OrganizationEventController::class, 'upcoming']);
	Route::get('/history', [OrganizationEventController::class, 'history']);
	Route::get('/{eventId}', [OrganizationEventController::class, 'detail']);
});
