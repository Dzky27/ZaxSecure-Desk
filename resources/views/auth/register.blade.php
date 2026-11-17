@extends('layouts.auth')

@section('title', 'Daftar Akun Baru — ZaxSecure Desk')

@section('auth-card')
<div class="auth-shell-center">
    <section class="glass auth-card">
        <div class="flex items-center justify-between">
            @include('components.logo', ['compact' => true, 'size' => 36])
            <span class="b b-user">Pendaftaran Pelapor</span>
        </div>

        <h1>Daftar Akun Pelapor Baru</h1>
        <p class="sub">Buat akun untuk mengirim laporan insiden keamanan siber dan memantau tindak lanjutnya.</p>

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

        <form id="registerForm" method="POST" action="{{ route('auth.register.store') }}" novalidate>
            @csrf

            <div class="field">
                <label class="label" for="name">
                    <span>Nama Lengkap <span class="req">*</span></span>
                </label>
                <input class="input" id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required>
                @error('name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="email">
                    <span>Alamat Email <span class="req">*</span></span>
                </label>
                <input class="input" id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" placeholder="nama@institusi.id" required>
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="password">
                    <span>Kata Sandi <span class="req">*</span></span>
                    <span class="opt">Min. 8 karakter</span>
                </label>
                <div class="pw">
                    <input class="input" id="password" name="password" type="password" autocomplete="new-password" placeholder="Buat kata sandi yang kuat" required minlength="8">
                    <button type="button" data-toggle-pass="password" aria-label="Tampilkan kata sandi">
                        <i data-lucide="eye"></i>
                    </button>
                </div>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="password_confirmation">
                    <span>Konfirmasi Kata Sandi <span class="req">*</span></span>
                </label>
                <div class="pw">
                    <input class="input" id="password_confirmation" name="password_confirmation" type="password" placeholder="Ketik ulang kata sandi" autocomplete="new-password" required minlength="8">
                    <button type="button" data-toggle-pass="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi">
                        <i data-lucide="eye"></i>
                    </button>
                </div>
            </div>

            <button class="btn btn-primary btn-block mt-2" type="submit">
                <i data-lucide="user-plus"></i>
                <span>Daftar & Masuk</span>
            </button>
        </form>

        <p class="field-help text-center mt-4">
            Catatan keamanan: Pendaftaran publik secara otomatis menetapkan peran sebagai <strong>Pengguna Pelapor</strong>. Hak akses Administrator tidak dapat dipilih melalui formulir ini.
        </p>

        <p class="auth-foot">
            Sudah memiliki akun? <a href="{{ route('auth.login') }}">Masuk di sini</a>
        </p>
    </section>
</div>
@endsection
