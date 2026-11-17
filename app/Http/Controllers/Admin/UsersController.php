<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount('incidents')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->input('search'));
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('role'), function ($q) use ($request) {
                $q->where('role', $request->input('role'));
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $isActive = $request->input('status') === 'active';
                $q->where('is_active', $isActive);
            })
            ->orderBy('role', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,user'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'role.required' => 'Peran pengguna wajib dipilih.',
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        AuditLogger::logActivity(
            'user.created',
            'User',
            $user->id,
            ['email' => $user->email, 'role' => $user->role],
            $request
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru (' . $user->name . ') berhasil ditambahkan.');
    }

    public function toggleStatus(Request $request, User $user)
    {
        // Prevent deactivating currently logged in admin or protected demo accounts
        if ((int) $user->id === (int) $request->user()->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang Anda gunakan.');
        }

        if (in_array(strtolower($user->email), ['admin@zaxsecure.id', 'user@zaxsecure.id'], true)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Akun demo utama dilindungi dan tidak dapat dinonaktifkan.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        AuditLogger::logActivity(
            $user->is_active ? 'user.activated' : 'user.deactivated',
            'User',
            $user->id,
            ['email' => $user->email, 'is_active' => $user->is_active],
            $request
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Status akun ' . $user->name . ' diperbarui menjadi ' . ($user->is_active ? 'Aktif' : 'Nonaktif') . '.');
    }
}
