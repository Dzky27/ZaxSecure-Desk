<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $systemSummary = [
            'php_version' => PHP_VERSION,
            'environment' => config('app.env'),
            'session_driver' => config('session.driver'),
            'session_lifetime' => config('session.lifetime'),
            'total_users' => User::count(),
            'active_categories' => IncidentCategory::where('is_active', true)->count(),
            'total_incidents' => Incident::count(),
        ];

        return view('admin.settings', compact('user', 'systemSummary'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah digunakan oleh pengguna lain.',
        ]);

        // Prevent changing demo account email address so demo login always works
        if (in_array(strtolower($user->email), ['admin@zaxsecure.id', 'user@zaxsecure.id'], true)) {
            $validated['email'] = $user->email;
        }

        $user->update([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
        ]);

        AuditLogger::logActivity('admin.profile_updated', 'User', $user->id, ['name' => $user->name], $request);

        return redirect()->route('admin.settings')
            ->with('success', 'Profil administrator berhasil diperbarui.');
    }
}
