<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\OrganizationDashboardController;

$prefix = env('PREFIX_API', 'v1') . "/organization-dashboard";

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
	Route::get('/', [OrganizationDashboardController::class, 'index']);
	Route::get('/bin-type', [OrganizationDashboardController::class, 'bintypes']);
	Route::get('/bin-type/{binTypeId}', [OrganizationDashboardController::class, 'binTypeRecycling']);
});
