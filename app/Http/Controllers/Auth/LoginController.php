<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            return redirect()->to($user->isAdmin() ? route('admin.dashboard') : route('user.dashboard'));
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Rate limiting reduces brute-force attempts.
        $key = 'login:' . $request->ip() . ':' . $email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            AuditLogger::logSecurity(
                'login_rate_limited',
                'high',
                "Percobaan login melampaui batas untuk email {$email}.",
                ['email' => $email, 'retry_after' => $seconds],
                $request
            );

            return back()->withInput($request->only('email', 'remember'))
                ->with('error', "Terlalu banyak upaya login. Coba lagi setelah {$seconds} detik.");
        }

        $credentials = [
            'email' => $email,
            'password' => $password,
            'is_active' => true,
        ];

        $authenticated = Auth::attempt($credentials, $remember);

        if (! $authenticated) {
            RateLimiter::hit($key, 60);

            AuditLogger::logSecurity(
                'login_failed',
                'medium',
                "Upaya login gagal untuk akun {$email}.",
                ['email' => $email],
                $request
            );

            return back()->withInput($request->only('email', 'remember'))
                ->with('error', 'Email, kata sandi, atau status akun tidak valid. Silakan coba lagi.');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        $user = Auth::user();
        AuditLogger::logActivity(
            'auth.login',
            'User',
            $user->id,
            ['role' => $user->role, 'email' => $user->email],
            $request,
            $user->id
        );

        $role = $user->isAdmin() ? 'admin' : 'user';
        $redirectTo = $role === 'admin' ? route('admin.dashboard') : route('user.dashboard');

        return redirect()->intended($redirectTo);
    }

    /**
     * Destroy the session and log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            AuditLogger::logActivity(
                'auth.logout',
                'User',
                $user->id,
                ['email' => $user->email],
                $request,
                $user->id
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('status', 'Anda telah berhasil keluar dari sesi.');
    }
}
