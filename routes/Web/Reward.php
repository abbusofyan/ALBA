<?php

use App\Http\Controllers\RewardController;
use Illuminate\Support\Facades\Route;

$prefix = 'rewards';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
    Route::get($prefix . '/export', [RewardController::class, 'export'])->name($prefix . '.export');
    Route::resource($prefix, RewardController::class);
	Route::get($prefix . '/download-vouchers/{reward}', [RewardController::class, 'downloadVouchers'])->name('rewards.downloadVouchers');
	Route::post($prefix . '/{reward}/update', [RewardController::class, 'update'])->name('rewards.update');
    Route::post($prefix . '/{reward}/toggleStatus', [RewardController::class, 'toggleStatus'])->name('rewards.toggleStatus');
});
