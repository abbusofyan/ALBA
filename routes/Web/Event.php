<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

$prefix = 'events';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
    Route::get($prefix . '/export', [EventController::class, 'export'])->name($prefix . '.export');
	Route::get($prefix . '/download-participant/{event}', [EventController::class, 'downloadParticipant'])->name($prefix . '.download-participant');
	Route::get($prefix . '/download-checkin-bins/{event}', [EventController::class, 'downloadCheckinBins'])->name($prefix . '.download-checkin-bins');
	Route::get($prefix . '/download-recyclings/{event}', [EventController::class, 'downloadRecyclings'])->name($prefix . '.download-recyclings');
	Route::get($prefix . '/download-import-template-view', [EventController::class, 'downloadTemplateView'])->name($prefix . '.download-import-template-view');
	Route::get($prefix . '/download-import-template/{eventTypeId}', [EventController::class, 'downloadTemplate'])->name($prefix . '.download-import-template');
	Route::get($prefix . '/{event}/recyclings', [EventController::class, 'recyclings'])->name($prefix . '.recyclings');
	Route::get($prefix . '/{event}/checkin-bins', [EventController::class, 'checkinBins'])->name($prefix . '.checkin-bins');
	Route::get($prefix . '/{event}/participants', [EventController::class, 'participants'])->name($prefix . '.participants');
	Route::post($prefix . '/import', [EventController::class, 'import'])->name('events.import');
    Route::resource($prefix, EventController::class);
    Route::post($prefix . '/{event}', [EventController::class, 'update'])->name('events.update');
    Route::post($prefix . '/{event}/toggleStatus', [EventController::class, 'toggleStatus'])->name('events.toggleStatus');
});
