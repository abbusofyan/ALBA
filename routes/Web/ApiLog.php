<?php

use App\Http\Controllers\ApiLogController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth'
])->group(function () {
    Route::get('/api-logs', [ApiLogController::class, 'showApiLogs'])->name('api.logs');
});
