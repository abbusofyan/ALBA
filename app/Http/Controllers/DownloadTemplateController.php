<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DownloadTemplateController extends Controller
{
    public function index() {
		return Inertia::render('Bin/DownloadTemplate');
	}
}
