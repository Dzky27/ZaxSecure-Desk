@extends('layouts.admin')

@section('title', 'Pengaturan Sistem — ZaxSecure Desk')

@section('page-content')
<div class="page-head">
    <div>
        <h1>Pengaturan Sistem & Kebijakan Keamanan</h1>
        <p class="sub">Kelola profil Administrator SOC serta tinjau parameter operasional dan postur keamanan platform.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" role="status">
        <i data-lucide="check-circle-2"></i>
        <div>{{ session('success') }}</div>
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

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Admin Profile Update -->
    <section class="glass card">
        <div class="card-h">
            <h2><i data-lucide="user-Check"></i>Profil Administrator Aktif</h2>
        </div>
        <form method="POST" action="{{ route('admin.settings.profile') }}">
            @csrf
            @method('PUT')
            <div class="field">
                <label class="label text-xs" for="admin_name">Nama Administrator <span class="req">*</span></label>
                <input type="text" id="admin_name" name="name" value="{{ old('name', $user?->name ?? 'Administrator') }}" class="input" required>
            </div>
            <div class="field">
                <label class="label text-xs" for="admin_email">Alamat Email</label>
                <input type="email" id="admin_email" name="email" value="{{ old('email', $user?->email ?? 'admin@zaxsecure.id') }}" class="input" required>
                <p class="field-help">Catatan: Email akun demo utama dilindungi agar akses demonstrasi tetap konsisten.</p>
            </div>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i>
                <span>Simpan Profil Admin</span>
            </button>
        </form>
    </section>

    <!-- Security & System Posture -->
    <section class="glass card">
        <div class="card-h">
            <h2><i data-lucide="shield-check"></i>Postur Keamanan & Lingkungan</h2>
        </div>
        <div class="rows text-xs">
            <div>
                <span>Status Proteksi CSRF</span>
                <span class="b b-ok">Aktif (Token Sesi Terverifikasi)</span>
            </div>
            <div>
                <span>Penyimpanan Lampiran Bukti</span>
                <span class="b b-ok">Disk Privat (Di Luar Direktori Publik)</span>
            </div>
            <div>
                <span>Pembatasan Percobaan Login (Rate Limiter)</span>
                <span class="b b-ok">Maks. 5 Upaya / Menit</span>
            </div>
            <div>
                <span>Batas Ukuran Unggahan Bukti</span>
                <span class="mono text-white">10 MB (PDF, JPG, PNG, WEBP, TXT)</span>
            </div>
            <div>
                <span>Masa Aktif Sesi</span>
                <span class="mono text-white">{{ $systemSummary['session_lifetime'] ?? 120 }} Menit</span>
            </div>
            <div>
                <span>Versi Runtime PHP</span>
                <span class="mono text-white">PHP {{ $systemSummary['php_version'] ?? PHP_VERSION }}</span>
            </div>
        </div>
    </section>
</div>
@endsection
