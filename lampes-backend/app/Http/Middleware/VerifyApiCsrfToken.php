<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyApiCsrfToken
{
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        // Bearer tokens are not sent automatically by browsers, so CSRF is enforced only for cookie/public form requests.
        if ($request->bearerToken()) {
            return $next($request);
        }

        $cookieName = config('security.csrf_cookie_name', 'XSRF-TOKEN');
        $cookieToken = (string) $request->cookie($cookieName, '');
        $headerToken = (string) ($request->header('X-CSRF-TOKEN') ?: $request->header('X-XSRF-TOKEN') ?: '');

        if ($cookieToken === '' || $headerToken === '' || ! hash_equals($cookieToken, $headerToken)) {
            return response()->json([
                'success' => false,
                'message' => 'CSRF token mismatch.',
            ], 419);
        }

        return $next($request);
    }
}
