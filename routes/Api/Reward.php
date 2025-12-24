<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\RewardController;

$prefix = env('PREFIX_API', 'v1') . "/rewards";

Route::post($prefix . '/getAll', [RewardController::class, 'getAll'])->name('rewards.getAll');

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
	Route::get('/detail/{reward}', [RewardController::class, 'detail'])->name('rewards.detail');
	Route::post('/{reward}/redeem', [RewardController::class, 'redeem'])->name('rewards.redeem');
});
