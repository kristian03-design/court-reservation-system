<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user() ?? auth()->guard('admin')->user() ?? auth()->guard('web')->user();

        if (! $user) {
            abort(401);
        }

        $userRole = $user->role;
        $hasAccess = false;

        foreach ($roles as $role) {
            if ($role === 'super_admin') {
                if ($userRole === 'super_admin') {
                    $hasAccess = true;
                }
            } elseif ($role === 'admin') {
                if (in_array($userRole, ['super_admin', 'admin'], true)) {
                    $hasAccess = true;
                }
            } elseif ($role === 'staff') {
                if (in_array($userRole, ['super_admin', 'admin', 'staff'], true)) {
                    $hasAccess = true;
                }
            } elseif (in_array($role, ['customer', 'user', 'player'], true)) {
                if (in_array($userRole, ['customer', 'user', 'player'], true)) {
                    $hasAccess = true;
                }
            } else {
                if ($userRole === $role) {
                    $hasAccess = true;
                }
            }
        }

        if (! $hasAccess) {
            // Auto redirect customer trying to visit admin pages to admin login
            if ((in_array('admin', $roles, true) || in_array('staff', $roles, true)) && ! $user->isAllowedAdminPanel()) {
                return redirect()->route('admin.login')->withErrors(['email' => 'Please login with an admin account to access the admin panel.']);
            }

            // Auto redirect admin trying to visit customer pages to customer login
            if (in_array('customer', $roles, true) && $user->isAllowedAdminPanel()) {
                return redirect()->route('login')->withErrors(['email' => 'Please login with a player account to access this page.']);
            }

            abort(403);
        }

        return $next($request);
    }
}
