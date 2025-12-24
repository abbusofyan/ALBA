<?php

use App\Http\Controllers\BinTypeController;
use Illuminate\Support\Facades\Route;

$prefix = 'bin-types';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
    Route::resource($prefix, BinTypeController::class);
	Route::post($prefix . '/{binType}', [BinTypeController::class, 'update'])->name('bin-types.update');
});
