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
    public function create(): View
    {
        return view('auth.login');
    }

    public function createAdmin(): View
    {
        return view('auth.admin-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        $route = $request->user()->isAdmin() ? 'admin.dashboard' : 'dashboard';

        return redirect()->intended(route($route));
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = User::where('email', $credentials['email'])->first();

        if (! $admin || ! Auth::validate($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'These admin credentials do not match our records.',
            ]);
        }

        if (! $admin->isAdmin() || ! $admin->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'This account is not allowed to access the admin panel.',
            ]);
        }

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

        if (now()->timestamp > $expiresAt) {
            $this->forgetAdminOtp($request);

            return redirect()->route('admin.login')
                ->withErrors(['otp' => 'Your admin OTP expired. Please login again.']);
        }

        if (! hash_equals($expectedOtp, $data['otp'])) {
            throw ValidationException::withMessages([
                'otp' => 'The admin OTP is incorrect.',
            ]);
        }

        $admin = User::find($userId);

        if (! $admin || ! $admin->isAdmin() || ! $admin->isActive()) {
            $this->forgetAdminOtp($request);

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'This account is not allowed to access the admin panel.']);
        }

        Auth::login($admin, (bool) $request->session()->get('admin_otp_remember'));
        $request->session()->regenerate();
        $this->forgetAdminOtp($request);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
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
