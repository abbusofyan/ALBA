<?php

use App\Http\Controllers\BinController;
use Illuminate\Support\Facades\Route;

$prefix = 'bins';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
	Route::get($prefix . '/download-import-template', [BinController::class, 'downloadTemplate'])->name('bins.downloadTemplate');
	Route::get($prefix . '/export', [BinController::class, 'export'])->name('bins.export');
	Route::post($prefix . '/import', [BinController::class, 'import'])->name('bins.import');
	Route::get($prefix . '/{bin}/recyclings', [BinController::class, 'recyclings'])->name($prefix . '.recyclings');
    Route::resource($prefix, BinController::class);
    Route::post($prefix . '/{bin}/toggleStatus', [BinController::class, 'toggleStatus'])->name('bins.toggleStatus');
	Route::post($prefix . '/{bin}/toggleVisibility', [BinController::class, 'toggleVisibility'])->name('bins.toggleVisibility');
	Route::post($prefix . '/getMapLocation', [BinController::class, 'getMapLocation']);
	Route::post($prefix . '/fetchDetailByIds', [BinController::class, 'fetchDetailByIds']);
});
