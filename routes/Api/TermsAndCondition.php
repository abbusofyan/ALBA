<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\TermsAndConditionController;

$prefix = env('PREFIX_API', 'v1') . "/tnc";

Route::group(['prefix' => $prefix], function () use ($prefix) {
    Route::get('/', [TermsAndConditionController::class, 'index']);
});
