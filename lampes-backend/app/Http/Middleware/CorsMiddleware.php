<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $frontendUrl = rtrim((string) config('security.frontend_url', 'http://localhost:3000'), '/');
        $origin = rtrim((string) $request->headers->get('Origin', ''), '/');

        $response = $request->isMethod('OPTIONS')
            ? response()->noContent()
            : $next($request);

        if ($origin !== '' && $origin === $frontendUrl) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        return $response
            ->header('Vary', 'Origin')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
