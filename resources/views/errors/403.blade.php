@extends('layouts.app')

@section('title', '403 Akses Ditolak — ZaxSecure Desk')

@section('content')
<div class="auth-shell-center">
    <section class="glass auth-card text-center">
        <div class="flex justify-center mb-4">
            <span class="stat-icon" style="width:52px;height:52px">
                <i data-lucide="shield-ban" style="width:26px;height:26px"></i>
            </span>
        </div>
        <span class="b b-pri-high">Kode HTTP 403</span>
        <h1 class="mt-3">Akses Ditolak</h1>
        <p class="sub">{{ $exception->getMessage() ?: 'Anda tidak memiliki izin atau wewenang peran untuk mengakses halaman atau sumber daya ini.' }}</p>
        <div class="flex justify-center gap-3 mt-4">
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-primary">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
            @else
                <a href="{{ route('auth.login') }}" class="btn btn-primary">
                    <i data-lucide="log-in"></i>
                    <span>Masuk ke Akun</span>
                </a>
            @endauth
        </div>
    </section>
</div>
@endsection
