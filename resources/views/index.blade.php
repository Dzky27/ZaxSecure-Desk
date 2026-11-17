@extends('layouts.app')

@section('title', 'ZaxSecure Desk — Platform Manajemen Insiden Keamanan Siber')

@section('content')
<div class="min-h-screen flex flex-col justify-between">
    <!-- Top Navigation -->
    <header class="border-b border-white/10 bg-[#0b111d]/90 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            @include('components.logo', ['compact' => true, 'size' => 36])
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-primary btn-sm">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Buka Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('auth.login') }}" class="btn btn-ghost btn-sm">Masuk</a>
                    <a href="{{ route('auth.register') }}" class="btn btn-primary btn-sm">
                        <i data-lucide="shield-plus"></i>
                        <span>Laporkan Insiden</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="max-w-6xl mx-auto px-6 py-14 flex-1">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-red-500/15 text-red-300 border border-red-500/30">
                    <i data-lucide="shield-alert" style="width:14px;height:14px"></i>
                    Sistem Manajemen & Respons Insiden Siber V2
                </span>
                <h1 class="text-3xl sm:text-5xl font-bold text-white tracking-tight leading-tight">
                    Pelaporan Aman. <span class="text-red-500">Respons Terukur.</span>
                </h1>
                <p class="text-slate-300 text-base leading-relaxed max-w-2xl">
                    ZaxSecure Desk menyediakan pusat komando terpadu untuk menerima laporan insiden keamanan siber, mengelola bukti digital secara privat, memantau linimasa mitigasi, serta menjaga akuntabilitas tim keamanan.
                </p>

                <div class="flex flex-wrap gap-3 pt-2">
                    @auth
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-primary">
                            <i data-lucide="arrow-right"></i>
                            <span>Lanjutkan ke Ruang Kerja</span>
                        </a>
                    @else
                        <a href="{{ route('auth.login') }}" class="btn btn-primary">
                            <i data-lucide="log-in"></i>
                            <span>Masuk ke Portal</span>
                        </a>
                        <a href="{{ route('auth.register') }}" class="btn btn-ghost">
                            <i data-lucide="user-plus"></i>
                            <span>Daftar Akun Pelapor</span>
                        </a>
                    @endauth
                </div>

                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-white/10">
                    <div>
                        <div class="text-2xl font-bold text-white">{{ $totalIncidents ?? 0 }}</div>
                        <div class="text-xs text-slate-400">Total Tiket Tercatat</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-emerald-400">{{ $resolvedIncidents ?? 0 }}</div>
                        <div class="text-xs text-slate-400">Insiden Terselesaikan</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-400">{{ $activeCategories ?? 0 }}</div>
                        <div class="text-xs text-slate-400">Kategori Ancaman Aktif</div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-4">
                <div class="glass card space-y-4">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3">
                        <h2 class="font-semibold text-white flex items-center gap-2 text-sm">
                            <i data-lucide="layers" class="text-red-400"></i>
                            Arsitektur Keamanan Berlapis
                        </h2>
                        <span class="b b-ok">Aktif</span>
                    </div>
                    <ul class="space-y-3 text-xs text-slate-300">
                        <li class="flex items-start gap-3">
                            <i data-lucide="check-circle-2" class="text-emerald-400 shrink-0 mt-0.5"></i>
                            <div><strong class="text-white">Kontrol Akses Berbasis Peran (RBAC):</strong> Pemisahan tegas antara hak akses Administrator SOC dan Pengguna Pelapor di tingkat server.</div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="check-circle-2" class="text-emerald-400 shrink-0 mt-0.5"></i>
                            <div><strong class="text-white">Isolasi Bukti Forensik:</strong> Lampiran bukti disimpan di luar direktori publik dan hanya dapat diunduh melalui otorisasi kebijakan insiden.</div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="check-circle-2" class="text-emerald-400 shrink-0 mt-0.5"></i>
                            <div><strong class="text-white">Jejak Audit Transaksional:</strong> Pembaruan status dan catatan linimasa diproses secara atomik dengan pencatatan log keamanan.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/10 py-6 text-center text-xs text-slate-500">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} ZaxSecure Desk V2 — Cybersecurity Incident Management Platform.</span>
            <span>Perlindungan CSRF, Sesi Terenkripsi &amp; Penyimpanan Bukti Privat Aktif.</span>
        </div>
    </footer>
</div>
@endsection
