@extends('layouts.auth')

@section('title', 'Setel Ulang Kata Sandi — ZaxSecure Desk')

@section('auth-card')
<div class="auth-shell-center">
    <section class="glass auth-card">
        @include('components.logo', ['compact' => true, 'size' => 36])

        <h1>Setel Ulang Kata Sandi</h1>
        <p class="sub">Buat kata sandi baru untuk mengamankan kembali akun Anda.</p>

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
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form id="resetForm" method="POST" action="{{ route('password.update') }}" novalidate>
            @csrf
            <input id="token" name="token" type="hidden" value="{{ $token }}">

            <div class="field">
                <label class="label" for="email">
                    <span>Alamat Email <span class="req">*</span></span>
                </label>
                <input class="input" id="email" name="email" type="email" autocomplete="email" value="{{ old('email', $email ?? '') }}" placeholder="nama@institusi.id" required>
            </div>

            <div class="field">
                <label class="label" for="password">
                    <span>Kata Sandi Baru <span class="req">*</span></span>
                    <span class="opt">Min. 8 karakter</span>
                </label>
                <div class="pw">
                    <input class="input" id="password" name="password" type="password" autocomplete="new-password" placeholder="Masukkan kata sandi baru" required minlength="8">
                    <button type="button" data-toggle-pass="password" aria-label="Tampilkan kata sandi">
                        <i data-lucide="eye"></i>
                    </button>
                </div>
            </div>

            <div class="field">
                <label class="label" for="password_confirmation">
                    <span>Konfirmasi Kata Sandi Baru <span class="req">*</span></span>
                </label>
                <div class="pw">
                    <input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Ulangi kata sandi baru" required minlength="8">
                    <button type="button" data-toggle-pass="password_confirmation" aria-label="Tampilkan kata sandi">
                        <i data-lucide="eye"></i>
                    </button>
                </div>
            </div>

            <button class="btn btn-primary btn-block" type="submit">
                <i data-lucide="key-round"></i>
                <span>Simpan Kata Sandi Baru</span>
            </button>
        </form>

        <p class="auth-foot">
            <a href="{{ route('auth.login') }}">&larr; Kembali ke Halaman Masuk</a>
        </p>
    </section>
</div>
@endsection
