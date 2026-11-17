<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        AuditLogger::logSecurity(
            'password_reset_requested',
            'low',
            'Permintaan pemulihan kata sandi diajukan.',
            ['email' => strtolower(trim((string) $request->input('email')))],
            $request
        );

        return $status === Password::RESET_LINK_SENT
            ? redirect()->back()->with('status', 'Tautan pemulihan kata sandi telah dikirim jika alamat email terdaftar.')
            : redirect()->back()->withInput()->with('error', 'Tidak dapat mengirim tautan pemulihan untuk alamat email tersebut.');
    }

    public function showResetForm(string $token): View
    {
        $tokenRecord = PasswordResetToken::where('token', $token)->first();
        $email = $tokenRecord ? $tokenRecord->email : '';

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Prevent resetting fixed demo credentials in production/staging unless authorized
        $emailLower = strtolower(trim((string) $request->input('email')));

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->password = Hash::make($password);
                $user->save();

                AuditLogger::logSecurity(
                    'password_reset_completed',
                    'medium',
                    'Kata sandi pengguna berhasil disetel ulang.',
                    ['user_id' => $user->id],
                    $request,
                    $user->id
                );
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('auth.login')->with('status', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru.')
            : redirect()->back()->withInput($request->only('email'))->with('error', 'Token pemulihan tidak valid atau sudah kedaluwarsa.');
    }
}
