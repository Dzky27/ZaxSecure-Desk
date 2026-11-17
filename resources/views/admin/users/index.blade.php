@extends('layouts.admin')

@section('title', 'Manajemen Pengguna — ZaxSecure Desk')

@section('page-content')
<div class="page-head">
    <div>
        <h1>Manajemen Pengguna</h1>
        <p class="sub">Kelola akun Administrator SOC dan Pengguna Pelapor beserta hak aksesnya.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" role="status">
        <i data-lucide="check-circle-2"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" role="alert">
        <i data-lucide="alert-circle"></i>
        <div>{{ session('error') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error" role="alert">
        <i data-lucide="alert-triangle"></i>
        <div>
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
    <div class="xl:col-span-2 space-y-5">
        <section class="glass card">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div class="sm:col-span-2">
                    <label class="label text-xs mb-1" for="q">Cari Pengguna</label>
                    <input class="input sm" id="q" name="search" type="search" value="{{ request('search') }}" placeholder="Nama atau alamat email...">
                </div>
                <div>
                    <label class="label text-xs mb-1" for="role">Peran</label>
                    <select id="role" name="role" class="select sm">
                        <option value="">Semua Peran</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Pengguna Pelapor</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-1 h-[35px]">
                        <i data-lucide="search"></i>
                        <span>Cari</span>
                    </button>
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm h-[35px]">
                            <i data-lucide="rotate-ccw"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <section class="glass card p-0 overflow-hidden">
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Nama Pengguna</th>
                            <th>Alamat Email</th>
                            <th>Peran</th>
                            <th>Jumlah Laporan</th>
                            <th>Status Akun</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>
                                    <div class="font-semibold text-white">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-400">Terdaftar: {{ $u->created_at?->format('d M Y') ?? '-' }}</div>
                                </td>
                                <td class="mono text-xs">{{ $u->email }}</td>
                                <td>
                                    @if($u->role === 'admin')
                                        <span class="b b-admin">Administrator</span>
                                    @else
                                        <span class="b b-user">Pelapor</span>
                                    @endif
                                </td>
                                <td class="mono text-xs">{{ $u->incidents_count ?? 0 }} tiket</td>
                                <td>
                                    @if($u->is_active)
                                        <span class="b b-ok">Aktif</span>
                                    @else
                                        <span class="b b-off">Nonaktif</span>
                                    @endif
                                </td>
                                <td style="text-align:right">
                                    @if((int) $u->id !== (int) auth()->id() && !in_array(strtolower($u->email), ['admin@zaxsecure.id', 'user@zaxsecure.id'], true))
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $u) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-ghost btn-sm">
                                                {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-500">Akun Dilindungi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty">Tidak ada data pengguna yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($users, 'hasPages') && $users->hasPages())
                <div class="px-5 py-4 border-t border-white/10 text-xs text-slate-400 flex items-center justify-between">
                    <span>Total: {{ $users->total() }} pengguna</span>
                    <div>{{ $users->links() }}</div>
                </div>
            @endif
        </section>
    </div>

    <!-- Add New User Form -->
    <section class="glass card">
        <div class="card-h">
            <h2><i data-lucide="user-plus"></i>Tambah Pengguna Baru</h2>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="field">
                <label class="label text-xs" for="new_name">Nama Lengkap <span class="req">*</span></label>
                <input type="text" id="new_name" name="name" value="{{ old('name') }}" class="input sm" placeholder="Nama lengkap personil" required>
            </div>
            <div class="field">
                <label class="label text-xs" for="new_email">Alamat Email <span class="req">*</span></label>
                <input type="email" id="new_email" name="email" value="{{ old('email') }}" class="input sm" placeholder="email@institusi.id" required>
            </div>
            <div class="field">
                <label class="label text-xs" for="new_role">Peran Sistem <span class="req">*</span></label>
                <select id="new_role" name="role" class="select sm" required>
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Pengguna Pelapor (User)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator SOC (Admin)</option>
                </select>
            </div>
            <div class="field">
                <label class="label text-xs" for="new_password">Kata Sandi Awal <span class="req">*</span></label>
                <input type="password" id="new_password" name="password" class="input sm" placeholder="Minimal 8 karakter" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <i data-lucide="plus"></i>
                <span>Simpan Pengguna</span>
            </button>
        </form>
    </section>
</div>
@endsection
