@extends('layouts.admin')

@section('title', 'Ubah Insiden ' . $incident->ticket_number . ' — ZaxSecure Desk')

@section('page-content')
<div class="max-w-3xl mx-auto">
    <div class="page-head">
        <div>
            <a href="{{ route('admin.incidents.show', $incident) }}" class="text-xs text-slate-400 hover:text-white block mb-1">&larr; Kembali ke Detail Insiden</a>
            <h1>Ubah Data & Triase Insiden</h1>
            <p class="sub">Nomor Tiket: <span class="mono text-red-400">{{ $incident->ticket_number }}</span></p>
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
        <form method="POST" action="{{ route('admin.incidents.update', $incident) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="field">
                    <label class="label" for="title">
                        <span>Judul Insiden <span class="req">*</span></span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $incident->title) }}" class="input" required maxlength="255">
                </div>

                <div class="field">
                    <label class="label" for="category_id">
                        <span>Kategori Ancaman <span class="req">*</span></span>
                    </label>
                    <select id="category_id" name="category_id" class="select" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $incident->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="field">
                    <label class="label" for="priority">
                        <span>Tingkat Prioritas <span class="req">*</span></span>
                    </label>
                    <select id="priority" name="priority" class="select" required>
                        @foreach($priorities as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', $incident->priority) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="label" for="status">
                        <span>Status Penanganan <span class="req">*</span></span>
                    </label>
                    <select id="status" name="status" class="select" required>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $incident->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="label" for="assigned_to">
                        <span>Analis Penanggung Jawab</span>
                        <span class="opt">Opsional</span>
                    </label>
                    <select id="assigned_to" name="assigned_to" class="select">
                        <option value="">-- Belum Ditugaskan --</option>
                        @foreach($admins ?? [] as $adm)
                            <option value="{{ $adm->id }}" {{ (int) old('assigned_to', $incident->assigned_to) === (int) $adm->id ? 'selected' : '' }}>{{ $adm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="field">
                    <label class="label" for="occurred_at">
                        <span>Tanggal Kejadian</span>
                    </label>
                    <input type="date" id="occurred_at" name="occurred_at" value="{{ old('occurred_at', $incident->occurred_at ? $incident->occurred_at->format('Y-m-d') : '') }}" class="input">
                </div>

                <div class="field">
                    <label class="label" for="related_url">
                        <span>URL / Endpoint Terdampak</span>
                    </label>
                    <input type="url" id="related_url" name="related_url" value="{{ old('related_url', $incident->related_url) }}" class="input">
                </div>
            </div>

            <div class="field">
                <label class="label" for="description">
                    <span>Deskripsi & Kronologi <span class="req">*</span></span>
                </label>
                <textarea id="description" name="description" rows="5" class="textarea" required>{{ old('description', $incident->description) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <a href="{{ route('admin.incidents.show', $incident) }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
