<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;

$prefix = 'menu';

Route::middleware([
    'auth'
])->group(function () use ($prefix) {
    Route::prefix($prefix)->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('menu.index');
        Route::get('/data', [MenuController::class, 'getData'])->name('menu.getData');
        Route::get('/create', [MenuController::class, 'create'])->name('menu.create');
        Route::get('/edit/{menu}', [MenuController::class, 'edit'])->name('menu.edit');
        Route::post('/store', [MenuController::class, 'store'])->name('menu.store');
        Route::post('/update/{menu}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/destroy/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
    });
});
