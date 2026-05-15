<?php

namespace App\Http\Middleware;

use App\Models\Client;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
    public function __construct(protected JwtService $jwtService) {}

    public function handle(Request $request, Closure $next)
    {
        $cookieName = config('security.auth_cookie_name', 'access_token');
        $token = $request->cookie($cookieName) ?: $request->bearerToken();

        if (! $token) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $payload = $this->jwtService->parseAndValidate($token);

        if (! $payload) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $client = Client::find($payload['sub']);

        if (! $client || ! hash_equals((string) $client->api_token, hash('sha256', $payload['jti']))) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        auth()->setUser($client);
        $request->setUserResolver(fn () => $client);

        return $next($request);
    }
}
