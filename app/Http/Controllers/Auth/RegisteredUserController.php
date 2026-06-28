<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => 'customer',
            'status' => 'active',
        ]);

        if (app()->environment('local')) {
            $user->markEmailAsVerified();
        } else {
            try {
                event(new Registered($user));
            } catch (Throwable $exception) {
                Log::warning('Registration verification email could not be sent.', [
                    'user_id' => $user->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        try {
            $user->notify(new \App\Notifications\WelcomeNotification());
        } catch (Throwable $exception) {
            Log::warning('Welcome email could not be sent.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }

        // Auth::guard('web')->login($user);

        return redirect()->route('login')->with('success', 'Registration successful! Please log in to your new account.');
    }
}
