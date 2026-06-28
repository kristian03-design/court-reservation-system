<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class EmailVerificationController extends Controller
{
    public function notice(): View|RedirectResponse
    {
        $request = request();

        return request()->user()->hasVerifiedEmail()
            ? redirect()->intended(route($this->homeRoute($request)))
            : view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->intended(route($this->homeRoute($request)))
            ->with('success', 'Email verified. Welcome to CourtConnect.');
    }

    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route($this->homeRoute($request));
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            Log::warning('Verification email could not be resent.', [
                'user_id' => $request->user()->id,
                'error' => $exception->getMessage(),
            ]);

            return back()->withErrors([
                'email' => 'The account was created, but the verification email could not be sent. Check the SMTP settings and try again.',
            ]);
        }

        return back()->with('success', 'Verification link sent.');
    }

    private function homeRoute(Request $request): string
    {
        return $request->user()->isAdmin() ? 'admin.dashboard' : 'dashboard';
    }
}
