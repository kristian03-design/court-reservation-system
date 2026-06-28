<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyRequestSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/*')) {
            $signature = $request->header('X-Signature');
            $timestamp = $request->header('X-Timestamp');
            $nonce = $request->header('X-Nonce');

            if (!$signature || !$timestamp || !$nonce) {
                return response()->json(['error' => 'Missing security signature headers.'], 401);
            }

            // Prevent replay attacks (reject if timestamp is older than 5 minutes)
            if (abs(time() - (int)$timestamp) > 300) {
                return response()->json(['error' => 'Request expired (potential replay attack).'], 401);
            }

            // Verify signature using SHA-256 and app key
            $secret = config('app.key');
            $expectedSignature = hash_hmac('sha256', $timestamp . $nonce . $request->getContent(), $secret);

            if (!hash_equals($expectedSignature, $signature)) {
                return response()->json(['error' => 'Invalid request signature.'], 401);
            }
        }

        return $next($request);
    }
}
