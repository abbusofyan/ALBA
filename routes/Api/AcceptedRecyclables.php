<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AcceptedRecyclableController;

$prefix = env('PREFIX_API', 'v1') . "/accepted-recyclables";

Route::group(['prefix' => $prefix], function () use ($prefix) {
	Route::post('/getAll', [AcceptedRecyclableController::class, 'getAll']);
});
