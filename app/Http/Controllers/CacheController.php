<?php

// app/Http/Controllers/CacheController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{
    public function clearCache(): \Illuminate\Http\JsonResponse
    {
        // Clear route cache
        Artisan::call('route:clear');

        // Clear config cache
        Artisan::call('config:clear');

        // Clear view cache
        Artisan::call('view:clear');

        // Clear application cache
        Artisan::call('cache:clear');

        // Additional cache clearing logic if needed

        return response()->json(['message' => 'Cache cleared successfully']);
    }
}
