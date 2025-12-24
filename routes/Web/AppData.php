<?php

use App\Http\Controllers\AppDataController;
use Illuminate\Support\Facades\Route;

$prefix = 'app-data';

Route::middleware([
    'auth'
])->group(function () use ($prefix) {
    Route::resource($prefix, AppDataController::class)->parameters([
        'app-data' => 'app_data',
    ]);
});
