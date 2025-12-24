<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ApiLogController extends Controller
{
    /**
     * Display the API call logs using Inertia.
     *
     * @return \Inertia\Response
     */
    public function showApiLogs()
    {
        $logFilePath = storage_path('logs/api_history.json');

        if (!file_exists($logFilePath)) {
            return Inertia::render('ApiLog/Index', [
                'logs' => [],
                'error' => 'Log file not found.',
            ]);
        }

        $logs = json_decode(file_get_contents($logFilePath), true);

        return Inertia::render('ApiLog/Index', [
            'logs' => $logs ?? [],
        ]);
    }
}
