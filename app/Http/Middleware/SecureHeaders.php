<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Avoid adding headers to binary file responses, etc.
        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'DENY');
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            if ($request->isSecure()) {
                $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            }
        }

        return $response;
    }
}
