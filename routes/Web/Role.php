<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

$prefix = 'role';

Route::middleware([
    'auth'
])->group(function () use ($prefix) {
    Route::prefix($prefix)->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('role.index');
        Route::get('/data', [RoleController::class, 'getData'])->name('role.getData');
        Route::get('/users', [RoleController::class, 'getUsers'])->name('role.getUsers');
        Route::post('/store', [RoleController::class, 'store'])->name('role.store');
        Route::put('/update/{role}', [RoleController::class, 'update'])->name('role.update');
        Route::delete('/destroy/{role}', [RoleController::class, 'destroy'])->name('role.destroy');
    });
});
