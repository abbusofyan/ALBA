<?php

use App\Http\Controllers\DownloadTemplateController;
use Illuminate\Support\Facades\Route;

$prefix = 'download-template';

Route::middleware([
    'auth',
])->group(function () use ($prefix) {
	Route::get($prefix . '/', [DownloadTemplateController::class, 'index']);
});
