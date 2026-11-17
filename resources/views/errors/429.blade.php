@extends('layouts.app')

@section('title', '429 Terlalu Banyak Permintaan — ZaxSecure Desk')

@section('content')
<div class="auth-shell-center">
    <section class="glass auth-card text-center">
        <div class="flex justify-center mb-4">
            <span class="stat-icon" style="width:52px;height:52px">
                <i data-lucide="shield-alert" style="width:26px;height:26px"></i>
            </span>
        </div>
        <span class="b b-pri-critical">Kode HTTP 429</span>
        <h1 class="mt-3">Terlalu Banyak Permintaan</h1>
        <p class="sub">Sistem membatasi frekuensi permintaan Anda untuk melindungi keamanan layanan. Silakan tunggu beberapa saat sebelum mencoba lagi.</p>
        <div class="flex justify-center gap-3 mt-4">
            <a href="{{ route('index') }}" class="btn btn-ghost">Kembali ke Beranda</a>
        </div>
    </section>
</div>
@endsection
