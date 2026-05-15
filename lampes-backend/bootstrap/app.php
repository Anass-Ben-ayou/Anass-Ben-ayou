<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiTokenMiddleware;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\VerifyApiCsrfToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Enregistrer les middlewares
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'auth.client' => ApiTokenMiddleware::class,
            'cors' => CorsMiddleware::class,
            'csrf.api' => VerifyApiCsrfToken::class,
        ]);

        // Ajouter CORS globalement
        $middleware->api(prepend: [
            CorsMiddleware::class,
            SecurityHeadersMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
