<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;

$prefix = 'permission';

Route::middleware([
    'auth'
])->group(function () use ($prefix) {
    Route::prefix($prefix)->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permission.index');       
        Route::get('/get-permissions', [PermissionController::class, 'getPermissions'])->name('permission.getPermissions');
        Route::post('/store', [PermissionController::class, 'store'])->name('permission.store');
        Route::put('/update/{permission}', [PermissionController::class, 'update'])->name('permission.update');
        Route::delete('/destroy/{permission}', [PermissionController::class, 'destroy'])->name('permission.destroy');
    });
});
