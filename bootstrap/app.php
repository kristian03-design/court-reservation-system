<?php

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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'redirect.admin' => \App\Http\Middleware\RedirectAdmin::class,
            'signature' => \App\Http\Middleware\VerifyRequestSignature::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SecureHeaders::class,
            \App\Http\Middleware\ContentSecurityPolicy::class,
            \App\Http\Middleware\SessionTimeout::class,
        ]);
        $middleware->redirectUsersTo(function ($request) {
            if (\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            }
            return route('dashboard');
        });
        $middleware->redirectGuestsTo(fn ($request) => $request->is('admin*') ? route('admin.login') : route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
