<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class
        ]);
        $middleware->group('api', [
			// \App\Http\Middleware\RedirectToProd::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
//            \Illuminate\Auth\Middleware\Authenticate::class,
        ]);
        $middleware->alias([
            'sanctum' => \App\Http\Middleware\Sanctum::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            if (! app()->environment(['local', 'testing']) && in_array($response->getStatusCode(), [500, 503, 404, 403, 401])) {
                return Inertia::render('Error', ['status' => $response->getStatusCode()])
                    ->toResponse($request)
                    ->setStatusCode($response->getStatusCode());
            }
            return $response;
        });

		$exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
		    if ($request->expectsJson() || $request->is('api/*')) {
		        return response()->json(['message' => 'Not Found'], 404);
		    }

		    return Inertia::render('Error', ['status' => 404])
		        ->toResponse($request)
		        ->setStatusCode(404);
		});
    })->create();
