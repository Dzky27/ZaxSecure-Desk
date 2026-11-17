@extends('layouts.auth')

@section('title', 'Masuk — ZaxSecure Desk')

@section('auth-card')
<section class="auth-v2">
    <!-- Left Column: Enterprise Branding & Platform Capabilities -->
    <div class="auth-v2__left">
        <a class="auth-v2__logo" href="{{ route('index') }}" aria-label="ZaxSecure Desk">
            <svg width="48" height="54" viewBox="0 0 48 54" fill="none" aria-hidden="true">
                <defs>
                    <linearGradient id="zsg" x1="8" y1="0" x2="40" y2="54" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#EF4444"/>
                        <stop offset="1" stop-color="#991B1B"/>
                    </linearGradient>
                </defs>
                <path d="M24 2 43 9v18c0 13-8.5 21-19 25C13.5 48 5 40 5 27V9L24 2Z" fill="url(#zsg)" stroke="rgba(255,255,255,.28)" stroke-width="1.2"/>
                <path d="m15.5 27 6 6.5L33 20" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="auth-v2__wm">
                <span>Zax<em>Secure</em></span>
                <span class="l2">Desk V2</span>
            </span>
        </a>

        <h1 class="auth-v2__headline">
            <span class="eyebrow">Pusat Operasi Keamanan Siber (SOC)</span>
            <span class="headline-main">Kelola & Tanggulangi Insiden Siber Secara Terstruktur</span>
        </h1>

        <p class="auth-v2__desc">
            Platform manajemen insiden keamanan informasi terpadu untuk pelaporan terenkripsi, triase prioritas ancaman, pelacakan bukti digital (forensik), dan koordinasi respons insiden secara transparan.
        </p>

        <div class="auth-v2__benefits">
            <div class="benefit">
                <span class="benefit-icon"><i data-lucide="shield-check"></i></span>
                <div class="benefit-text">
                    <strong>Triase & Klasifikasi Cepat</strong>
                    Prioritaskan laporan insiden dari tingkat Rendah hingga Kritis disertai penugasan personil tim respons.
                </div>
            </div>
            <div class="benefit">
                <span class="benefit-icon"><i data-lucide="file-lock-2"></i></span>
                <div class="benefit-text">
                    <strong>Penyimpanan Bukti Terisolasi</strong>
                    Lampiran bukti insiden disimpan pada ruang privat terproteksi dengan validasi tipe berkas (MIME) dan hash integritas.
                </div>
            </div>
            <div class="benefit">
                <span class="benefit-icon"><i data-lucide="activity"></i></span>
                <div class="benefit-text">
                    <strong>Linimasa & Jejak Audit</strong>
                    Setiap perubahan status, catatan investigasi internal maupun publik, serta aktivitas pengguna tercatat secara akurat.
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Login Card & Demo Credentials -->
    <div class="auth-v2__right">
        <div class="auth-v2__card">
            <div class="auth-v2__mobile-brand">
                @include('components.logo', ['compact' => true, 'size' => 34])
                <span class="b b-admin">SOC Platform</span>
            </div>

            <h2 class="auth-v2__title">Masuk ke Akun Anda</h2>
            <p class="auth-v2__subtitle">Masukkan kredensial terdaftar atau gunakan akses akun demo di bawah.</p>

            @if(session('error'))
                <div class="alert alert-error" role="alert">
                    <i data-lucide="alert-circle"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-success" role="status">
                    <i data-lucide="check-circle-2"></i>
                    <div>{{ session('status') }}</div>
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

            <form id="loginForm" method="POST" action="{{ route('auth.login.store') }}" novalidate>
                @csrf

                <div class="field">
                    <label class="auth-v2__label" for="email">
                        <span class="auth-v2__label-text">
                            <i data-lucide="mail" class="field-icon"></i>
                            <span>Alamat Email <span class="text-red">*</span></span>
                        </span>
                    </label>
                    <div class="input-wrap">
                        <input
                            class="auth-v2__input @error('email') invalid @enderror"
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            placeholder="nama@institusi.id"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                        <p class="field-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label class="auth-v2__label" for="password">
                        <span class="auth-v2__label-text">
                            <i data-lucide="lock" class="field-icon"></i>
                            <span>Kata Sandi <span class="text-red">*</span></span>
                        </span>
                    </label>
                    <div class="input-wrap pw-wrap">
                        <input
                            class="auth-v2__input @error('password') invalid @enderror"
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Masukkan kata sandi Anda"
                            autocomplete="current-password"
                            required
                        >
                        <button
                            type="button"
                            class="visibility-toggle"
                            data-toggle-pass="password"
                            aria-label="Tampilkan kata sandi"
                            title="Tampilkan / sembunyikan kata sandi"
                        >
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-options">
                    <label class="check" for="remember">
                        <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <span>Ingat sesi saya</span>
                    </label>
                    <a class="forgot-link" href="{{ route('auth.forgot-password') }}">Lupa kata sandi?</a>
                </div>

                <button class="btn-auth" type="submit" id="loginBtn">
                    <span class="btn-content">
                        <span class="btn-spinner" id="loginSpinner" aria-hidden="true"></span>
                        <span class="btn-text" id="loginBtnText">Masuk ke Sistem</span>
                    </span>
                </button>
            </form>

            <p class="auth-v2__status" id="loginStatus" role="status" aria-live="polite"></p>

            <!-- Demo Credentials Section -->
            <div class="demo-access">
                <div class="demo-header">
                    <span class="demo-badge">Akses Akun Demo</span>
                    <button class="btn-copy-credentials" id="copyAllDemo" type="button">
                        <i data-lucide="copy" style="width:13px;height:13px"></i>
                        <span>Salin Semua Kredensial</span>
                    </button>
                </div>
                <p class="demo-desc">Gunakan kredensial di bawah ini untuk menguji fitur Administrator maupun Pengguna Pelapor:</p>

                <div class="demo-grid">
                    <!-- ADMIN DEMO CARD -->
                    <div class="demo-card demo-card-admin">
                        <div>
                            <div class="demo-card-header">
                                <span class="demo-card-role">ADMIN</span>
                                <span class="demo-card-tag">Administrator SOC</span>
                            </div>
                            <div class="demo-cred-row mb-2">
                                <span class="demo-label">Email</span>
                                <span class="demo-value" id="adminEmailVal">admin@zaxsecure.id</span>
                            </div>
                            <div class="demo-cred-row">
                                <span class="demo-label">Kata Sandi</span>
                                <span class="demo-value demo-pw-value" id="adminPwVal">ChangeMe123!</span>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn-copy" id="copyAdminEmail" type="button" data-copy="admin@zaxsecure.id" data-fill-email="admin@zaxsecure.id">
                                <span>Salin Email</span>
                            </button>
                            <button class="btn-copy" id="copyAdminPassword" type="button" data-copy="ChangeMe123!" data-fill-pw="ChangeMe123!">
                                <span>Salin Sandi</span>
                            </button>
                        </div>
                    </div>

                    <!-- USER DEMO CARD -->
                    <div class="demo-card demo-card-user">
                        <div>
                            <div class="demo-card-header">
                                <span class="demo-card-role">USER</span>
                                <span class="demo-card-tag">Pelapor Insiden</span>
                            </div>
                            <div class="demo-cred-row mb-2">
                                <span class="demo-label">Email</span>
                                <span class="demo-value" id="userEmailVal">user@zaxsecure.id</span>
                            </div>
                            <div class="demo-cred-row">
                                <span class="demo-label">Kata Sandi</span>
                                <span class="demo-value demo-pw-value" id="userPwVal">TestUser123!</span>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn-copy" id="copyUserEmail" type="button" data-copy="user@zaxsecure.id" data-fill-email="user@zaxsecure.id">
                                <span>Salin Email</span>
                            </button>
                            <button class="btn-copy" id="copyUserPassword" type="button" data-copy="TestUser123!" data-fill-pw="TestUser123!">
                                <span>Salin Sandi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <p class="auth-v2__signup">
                Belum memiliki akun pelapor?
                <a href="{{ route('auth.register') }}">Daftar Akun Baru</a>
            </p>
        </div>
    </div>
</section>
@endsection
