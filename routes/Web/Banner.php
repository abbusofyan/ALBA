<?php

use App\Http\Controllers\BannerController;
use Illuminate\Support\Facades\Route;

$prefix = 'banners';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
	Route::resource($prefix, BannerController::class);
	Route::post('/update/{bannerPlacement}', [BannerController::class, 'update'])->name('banners.update');
	Route::post($prefix . '/{bannerPlacement}/toggleStatus', [BannerController::class, 'toggleStatus'])->name('banners.toggleStatus');
});
