<?php

use App\Http\Controllers\EnterpriseController;
use Illuminate\Support\Facades\Route;

$prefix = 'enterprises';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
	Route::get($prefix . '/export', [EnterpriseController::class, 'export'])->name('enterprises.export');
    Route::resource($prefix, EnterpriseController::class);
	Route::post($prefix . '/{enterprise}/toggleStatus', [EnterpriseController::class, 'toggleStatus'])->name('enterprises.toggleStatus');
});
