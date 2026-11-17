<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('user.profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal 3 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah digunakan.',
            'current_password.required_with' => 'Kata sandi saat ini wajib diisi untuk mengubah kata sandi.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $isDemoAccount = in_array(strtolower($user->email), ['admin@zaxsecure.id', 'user@zaxsecure.id'], true);

        if (! empty($validated['new_password'])) {
            if ($isDemoAccount) {
                return back()->with('error', 'Kata sandi akun demo dilindungi dan tidak dapat diubah.');
            }

            if (! Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
            }

            $user->password = Hash::make($validated['new_password']);
        }

        $user->name = trim($validated['name']);
        if (! $isDemoAccount) {
            $user->email = strtolower(trim($validated['email']));
        }
        $user->save();

        AuditLogger::logActivity('user.profile_updated', 'User', $user->id, ['name' => $user->name], $request);

        return redirect()->route('user.profile')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
