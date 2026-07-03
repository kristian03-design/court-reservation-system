<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authenticates API requests via a JWT Bearer token.
 * Sets auth()->user() so controllers can call $request->user() normally.
 */
class JwtAuthenticate
{
    public function __construct(private JwtService $jwt) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->jwt->extractBearerToken($request);

        if (! $token) {
            return response()->json(['message' => 'Unauthenticated. No Bearer token provided.'], 401);
        }

        $payload = $this->jwt->decodeOrNull($token);

        if (! $payload) {
            return response()->json(['message' => 'Token is invalid or expired.'], 401);
        }

        if (($payload->type ?? '') !== 'access') {
            return response()->json(['message' => 'Refresh tokens cannot be used for API access.'], 401);
        }

        $user = User::find($payload->sub);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 401);
        }

        if (! $user->isActive()) {
            return response()->json(['message' => 'Account is suspended.'], 403);
        }

        // Bind the user to the request so $request->user() works in controllers
        auth()->setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
