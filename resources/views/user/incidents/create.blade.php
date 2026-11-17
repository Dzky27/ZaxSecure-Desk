@extends('layouts.user')

@section('title', 'Buat Laporan Insiden — ZaxSecure Desk')

@section('page-content')
<div class="max-w-2xl mx-auto">
    <div class="page-head">
        <div>
            <a href="{{ route('user.incidents.index') }}" class="text-xs text-slate-400 hover:text-white block mb-1">&larr; Kembali ke Laporan Saya</a>
            <h1>Buat Laporan Insiden Baru</h1>
            <p class="sub">Laporkan temuan indikasi ancaman atau insiden keamanan siber kepada tim respons SOC.</p>
        </div>
    </div>

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

    <section class="glass card">
        <form method="POST" action="{{ route('user.incidents.store') }}">
            @csrf

            <div class="field">
                <label class="label" for="category_id">
                    <span>Kategori Insiden <span class="req">*</span></span>
                </label>
                <select id="category_id" name="category_id" class="select" required>
                    <option value="">-- Pilih Kategori Ancaman --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <p class="field-help">Pilih kategori yang paling menggambarkan insiden yang Anda temukan.</p>
            </div>

            <div class="field">
                <label class="label" for="title">
                    <span>Judul Laporan <span class="req">*</span></span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" class="input" placeholder="Contoh: Email Phishing Mengatasnamakan Bagian Keuangan" required maxlength="255">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="field">
                    <label class="label" for="occurred_at">
                        <span>Perkiraan Tanggal Kejadian</span>
                        <span class="opt">Opsional</span>
                    </label>
                    <input type="date" id="occurred_at" name="occurred_at" value="{{ old('occurred_at', date('Y-m-d')) }}" class="input">
                </div>

                <div class="field">
                    <label class="label" for="related_url">
                        <span>URL / Tautan Terkait</span>
                        <span class="opt">Opsional</span>
                    </label>
                    <input type="url" id="related_url" name="related_url" value="{{ old('related_url') }}" placeholder="https://contoh-tautan-mencurigakan.com" class="input">
                </div>
            </div>

            <div class="field">
                <label class="label" for="description">
                    <span>Kronologi & Deskripsi Kejadian <span class="req">*</span></span>
                    <span class="opt">Maks. 500 karakter</span>
                </label>
                <textarea id="description" name="description" rows="5" maxlength="500" class="textarea" placeholder="Jelaskan apa yang terjadi, kapan ditemukan, serta dampak yang terlihat..." required>{{ old('description') }}</textarea>
                <p class="field-help">Catatan: Tingkat prioritas dan status penanganan akan ditetapkan oleh Administrator SOC setelah triase awal. Anda juga dapat mengunggah berkas bukti setelah laporan dibuat.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <a href="{{ route('user.incidents.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="send"></i>
                    <span>Kirim Laporan Insiden</span>
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
