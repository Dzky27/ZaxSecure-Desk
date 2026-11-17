@extends('layouts.app')

@section('title', '500 Gangguan Sistem — ZaxSecure Desk')

@section('content')
<div class="auth-shell-center">
    <section class="glass auth-card text-center">
        <div class="flex justify-center mb-4">
            <span class="stat-icon" style="width:52px;height:52px">
                <i data-lucide="server-crash" style="width:26px;height:26px"></i>
            </span>
        </div>
        <span class="b b-pri-high">Kode HTTP 500</span>
        <h1 class="mt-3">Terjadi Kendala Internal</h1>
        <p class="sub">Maaf, permintaan Anda tidak dapat diproses saat ini. Detail teknis telah dicatat secara internal untuk ditindaklanjuti oleh tim teknis.</p>
        <div class="flex justify-center gap-3 mt-4">
            <a href="{{ route('index') }}" class="btn btn-primary">
                <i data-lucide="home"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </section>
</div>
@endsection
