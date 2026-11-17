@extends('layouts.auth')

@section('title', 'Pemulihan Kata Sandi — ZaxSecure Desk')

@section('auth-card')
<div class="auth-shell-center">
    <section class="glass auth-card">
        @include('components.logo', ['compact' => true, 'size' => 36])

        <h1>Pemulihan Kata Sandi</h1>
        <p class="sub">Masukkan alamat email akun Anda untuk menerima tautan pengaturan ulang kata sandi.</p>

        @if(session('status'))
            <div class="alert alert-success" role="status">
                <i data-lucide="check-circle-2"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

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

        <form id="forgotForm" method="POST" action="{{ route('password.email') }}" novalidate>
            @csrf
            <div class="field">
                <label class="label" for="email">
                    <span>Alamat Email Terdaftar <span class="req">*</span></span>
                </label>
                <input class="input" id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" placeholder="nama@institusi.id" required autofocus>
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <button class="btn btn-primary btn-block" type="submit">
                <i data-lucide="send"></i>
                <span>Kirim Tautan Pemulihan</span>
            </button>
        </form>

        <p class="auth-foot">
            <a href="{{ route('auth.login') }}">&larr; Kembali ke Halaman Masuk</a>
        </p>
    </section>
</div>
@endsection
