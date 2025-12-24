<?php

use App\Http\Controllers\RecyclingController;
use Illuminate\Support\Facades\Route;

$prefix = 'recyclings';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
    Route::get($prefix . '/export', [RecyclingController::class, 'export'])->name($prefix . '.export');
	Route::get($prefix . '/exportStream', [RecyclingController::class, 'exportStream'])->name($prefix . '.exportStream');
    Route::resource($prefix, RecyclingController::class);
});
