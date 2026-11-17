@extends('layouts.app')

@section('title', '404 Halaman Tidak Ditemukan — ZaxSecure Desk')

@section('content')
<div class="auth-shell-center">
    <section class="glass auth-card text-center">
        <div class="flex justify-center mb-4">
            <span class="stat-icon" style="width:52px;height:52px">
                <i data-lucide="file-question" style="width:26px;height:26px"></i>
            </span>
        </div>
        <span class="b b-st-pending">Kode HTTP 404</span>
        <h1 class="mt-3">Halaman Tidak Ditemukan</h1>
        <p class="sub">Halaman atau berkas bukti yang Anda cari tidak tersedia atau telah dipindahkan.</p>
        <div class="flex justify-center gap-3 mt-4">
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-primary">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
            @else
                <a href="{{ route('index') }}" class="btn btn-primary">
                    <i data-lucide="home"></i>
                    <span>Halaman Utama</span>
                </a>
            @endauth
        </div>
    </section>
</div>
@endsection
