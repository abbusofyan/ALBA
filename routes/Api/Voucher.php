<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\VoucherController;

$prefix = env('PREFIX_API', 'v1') . "/vouchers";

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
	Route::get('/detail/{voucher}', [VoucherController::class, 'detail'])->name('vouchers.detail');
});
