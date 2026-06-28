<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function createAdmin(Request $request): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.admin-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $throttleKey = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($credentials['email']).'|'.$request->ip());

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $user = User::where('email', $credentials['email'])->first();

        if ($user?->isAdmin()) {
            throw ValidationException::withMessages([
                'email' => 'Admin accounts must use the admin login page.',
            ]);
        }

        if (! Auth::guard('web')->attempt($credentials, $remember)) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 300); // 5 min lockout

            // Log security attempt
            Log::channel('security')->warning('Failed login attempt.', [
                'email' => $credentials['email'],
                'ip' => $request->ip()
            ]);

            throw ValidationException::withMessages([
                'email' => 'Incorrect email or password. Please try again.',
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = \Illuminate\Support\Str::transliterate('admin_login|'.\Illuminate\Support\Str::lower($credentials['email']).'|'.$request->ip());

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Too many admin login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $admin = User::where('email', $credentials['email'])->first();

        if (! $admin || ! Auth::guard('admin')->validate($credentials)) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 300);
            Log::channel('security')->warning('Failed admin login attempt.', [
                'email' => $credentials['email'],
                'ip' => $request->ip()
            ]);
            throw ValidationException::withMessages([
                'email' => 'Incorrect admin email or password. Please try again.',
            ]);
        }

        if (! $admin->isAllowedAdminPanel() || ! $admin->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'This account is not allowed to access the admin panel.',
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        $otp = (string) random_int(100000, 999999);

        try {
            Mail::raw(
                "Your CourtConnect admin OTP is {$otp}. This code expires in 10 minutes.",
                function ($message) use ($admin) {
                    $message->to($admin->email)
                        ->subject('CourtConnect Admin OTP');
                }
            );
        } catch (Throwable $exception) {
            Log::warning('Admin OTP email could not be sent.', [
                'admin_id' => $admin->id,
                'error' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'The admin OTP could not be sent. Please check the Gmail SMTP settings.',
            ]);
        }

        $request->session()->put([
            'admin_otp_user_id' => $admin->id,
            'admin_otp_email' => $admin->email,
            'admin_otp_code' => $otp,
            'admin_otp_remember' => $request->boolean('remember'),
            'admin_otp_expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        return redirect()->route('admin.otp')->with('success', 'We sent a 6-digit OTP to your admin email.');
    }

    public function createAdminOtp(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('admin_otp_user_id')) {
            return redirect()->route('admin.login');
        }

        return view('auth.admin-otp', [
            'email' => $request->session()->get('admin_otp_email'),
        ]);
    }

    public function verifyAdminOtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $userId = $request->session()->get('admin_otp_user_id');
        $expectedOtp = $request->session()->get('admin_otp_code');
        $expiresAt = $request->session()->get('admin_otp_expires_at');

        if (! $userId || ! $expectedOtp || ! $expiresAt) {
            return redirect()->route('admin.login')
                ->withErrors(['otp' => 'Please login again to request a new admin OTP.']);
        }

        $throttleKey = 'admin_otp|'.$userId.'|'.$request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'otp' => "Too many incorrect OTP attempts. Please wait {$seconds} seconds before trying again.",
            ]);
        }

        if (now()->timestamp > $expiresAt) {
            $this->forgetAdminOtp($request);

            return redirect()->route('admin.login')
                ->withErrors(['otp' => 'Your admin OTP expired. Please login again.']);
        }

        if (! hash_equals($expectedOtp, $data['otp'])) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 300);
            Log::channel('security')->warning('Incorrect OTP entered for admin.', [
                'user_id' => $userId,
                'ip' => $request->ip()
            ]);
            throw ValidationException::withMessages([
                'otp' => 'The admin OTP is incorrect.',
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        $admin = User::find($userId);

        if (! $admin || ! $admin->isAllowedAdminPanel() || ! $admin->isActive()) {
            $this->forgetAdminOtp($request);

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'This account is not allowed to access the admin panel.']);
        }

        Auth::guard('admin')->login($admin, (bool) $request->session()->get('admin_otp_remember'));
        $request->session()->regenerate();
        $this->forgetAdminOtp($request);

        return redirect()->route('admin.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        if (! Auth::guard('admin')->check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } else {
            $request->session()->regenerate();
        }

        return redirect()->route('home');
    }

    public function destroyAdmin(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        if (! Auth::guard('web')->check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } else {
            $request->session()->regenerate();
        }

        return redirect()->route('admin.login');
    }

    private function forgetAdminOtp(Request $request): void
    {
        $request->session()->forget([
            'admin_otp_user_id',
            'admin_otp_email',
            'admin_otp_code',
            'admin_otp_remember',
            'admin_otp_expires_at',
        ]);
    }
}
