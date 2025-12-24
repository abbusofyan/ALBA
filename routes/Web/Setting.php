<?php

use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings/points', [SettingController::class, 'storePoints'])->name('settings.points.store');
