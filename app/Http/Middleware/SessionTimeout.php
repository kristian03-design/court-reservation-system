<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $guards = ['web', 'admin'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $lastActivity = $request->session()->get("last_activity_{$guard}");
                $timeout = $guard === 'admin' ? 15 * 60 : 30 * 60; // 15 mins for admin, 30 for web

                if ($lastActivity && (time() - $lastActivity > $timeout)) {
                    Auth::guard($guard)->logout();
                    $request->session()->forget("last_activity_{$guard}");

                    if (!Auth::guard('web')->check() && !Auth::guard('admin')->check()) {
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                    }

                    $msg = 'Your session has expired due to inactivity. Please log in again.';
                    return $guard === 'admin'
                        ? redirect()->route('admin.login')->withErrors(['email' => $msg])
                        : redirect()->route('login')->withErrors(['email' => $msg]);
                }

                $request->session()->put("last_activity_{$guard}", time());
            }
        }

        return $next($request);
    }
}
