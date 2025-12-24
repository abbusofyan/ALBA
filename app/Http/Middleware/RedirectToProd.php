<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Http;
use Closure;

class RedirectToProd
{
    public function handle($request, Closure $next)
    {
        // Take the incoming path and forward it
        // $prodBaseUrl = 'https://app.step-up.sg';
        $targetUrl = rtrim($prodBaseUrl, '/') . $request->getRequestUri();

        $http = Http::withToken($request->bearerToken());

        // Forward files if they exist
        if ($request->files->count() > 0) {
            foreach ($request->files as $key => $file) {
                if ($file->isValid()) {
                    $http = $http->attach(
                        $key,
                        fopen($file->getRealPath(), 'r'),
                        $file->getClientOriginalName()
                    );
                }
            }

            // Send with other form fields
            $response = $http->post($targetUrl, $request->except(array_keys($request->files->all())));
        } else {
            // No files: forward body as JSON or form
            if ($request->isJson()) {
                $response = $http->withHeaders([
                    'Content-Type' => 'application/json',
                ])->send($request->method(), $targetUrl, [
                    'json' => $request->all(),
                ]);
            } else {
                $response = $http->send($request->method(), $targetUrl, [
                    'form_params' => $request->all(),
                ]);
            }
        }

        // Only keep safe headers
        $safeHeaders = collect($response->headers())->only([
            'Content-Type'
        ])->toArray();

        return response($response->body(), $response->status())
            ->withHeaders($safeHeaders);
    }
}
