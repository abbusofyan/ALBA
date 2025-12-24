<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\EventController;

$prefix = env('PREFIX_API', 'v1') . "/events";

Route::get($prefix . '/', [EventController::class, 'get'])->name('events.get');

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
	Route::get('/{event}', [EventController::class, 'detail'])->name('events.detail');
	Route::post('/join', [EventController::class, 'join'])->name('events.join');
	Route::get('/{id}/leaderboard', [EventController::class, 'leaderboard'])->name('events.leaderboard');
});
