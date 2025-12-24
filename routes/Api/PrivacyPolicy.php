<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\PrivacyPolicyController;

$prefix = env('PREFIX_API', 'v1') . "/privacy-policy";

Route::group(['prefix' => $prefix], function () use ($prefix) {
    Route::get('/', [PrivacyPolicyController::class, 'index']);
});
