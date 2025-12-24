<?php

use App\Http\Controllers\RewardController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/rewards/{reward}/vouchers', [RewardController::class, 'vouchers'])->name('rewards.vouchers');
});
