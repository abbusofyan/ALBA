<?php

use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

$prefix = 'staffs';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
    Route::resource($prefix, StaffController::class);
	Route::post('staffs/{staff}/toggleStatus', [StaffController::class, 'toggleStatus'])->name('staffs.toggleStatus');
});
