<?php

use App\Http\Controllers\SchoolController;
use Illuminate\Support\Facades\Route;

$prefix = 'schools';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
	Route::get($prefix . '/export', [SchoolController::class, 'export'])->name('schools.export');
    Route::resource($prefix, SchoolController::class);
	Route::post($prefix . '/{school}/toggleStatus', [SchoolController::class, 'toggleStatus'])->name('schools.toggleStatus');
});
