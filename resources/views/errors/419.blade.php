@extends('layouts.app')

@section('title', '419 Sesi Kedaluwarsa — ZaxSecure Desk')

@section('content')
<div class="auth-shell-center">
    <section class="glass auth-card text-center">
        <div class="flex justify-center mb-4">
            <span class="stat-icon" style="width:52px;height:52px">
                <i data-lucide="clock-alert" style="width:26px;height:26px"></i>
            </span>
        </div>
        <span class="b b-st-pending">Kode HTTP 419</span>
        <h1 class="mt-3">Sesi Keamanan Kedaluwarsa</h1>
        <p class="sub">Token keamanan (CSRF) untuk sesi Anda telah habis masa berlakunya. Silakan muat ulang halaman dan coba kembali.</p>
        <div class="flex justify-center gap-3 mt-4">
            <a href="{{ route('auth.login') }}" class="btn btn-primary">
                <i data-lucide="refresh-cw"></i>
                <span>Muat Ulang Halaman Masuk</span>
            </a>
        </div>
    </section>
</div>
@endsection
