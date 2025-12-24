<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    protected $logFilePath;
    protected $maxEntries;

    public function __construct()
    {
        $this->logFilePath = storage_path('logs/api_history.json');
        $this->maxEntries = 1000;
    }

    public function result($data = null, $message = null, $code = '200')
    {
        $result = [];
        if (!empty($data)) $result['data'] = $data;
        if (!empty($message)) $result['message'] = $message;
        $result['code'] = (string)$code;
        return response()->json($result, $code);
    }

    /**
     * Log the API call payload and response.
     *
     * @param array $payload
     * @param array $response
     * @param int $retry
     * @return void
     */
    public function logApiCall(array $payload, array $response, $retry = 0): void
    {
        try {
            $logs = [];

            if (file_exists($this->logFilePath)) {
                // Retrieve the current logs
                $json = file_get_contents($this->logFilePath);
                $logs = json_decode($json, true) ?? [];
            }

            $logs[] = [
                'timestamp' => Carbon::now()->toDateTimeString(),
                'retry' => $retry,
                'payload' => $payload,
                'response' => $response,
            ];

            if (count($logs) > $this->maxEntries) {
                // Remove the oldest entries
                $logs = array_slice($logs, -$this->maxEntries);
            }

            $json = json_encode($logs, JSON_PRETTY_PRINT);

            file_put_contents($this->logFilePath, $json, LOCK_EX);
        } catch (\Exception $e) {
            Log::error('Failed to log NetSuite API call: ' . $e->getMessage());
        }
    }
}
