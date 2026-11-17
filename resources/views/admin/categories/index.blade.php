@extends('layouts.admin')

@section('title', 'Kategori Insiden — ZaxSecure Desk')

@section('page-content')
<div class="page-head">
    <div>
        <h1>Kategori Insiden</h1>
        <p class="sub">Kelola klasifikasi ancaman keamanan siber untuk pengelompokan laporan insiden.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" role="status">
        <i data-lucide="check-circle-2"></i>
        <div>{{ session('success') }}</div>
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
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
    <div class="xl:col-span-2">
        <section class="glass card p-0 overflow-hidden">
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Deskripsi Klasifikasi</th>
                            <th>Jumlah Insiden</th>
                            <th>Status</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr>
                                <td>
                                    <div class="font-semibold text-white">{{ $cat->name }}</div>
                                    <div class="mono text-[11px] text-slate-400">Slug: {{ $cat->slug }}</div>
                                </td>
                                <td class="text-xs text-slate-300 max-w-md">{{ $cat->description ?: '—' }}</td>
                                <td class="mono text-xs">{{ $cat->incidents_count ?? 0 }} tiket</td>
                                <td>
                                    @if($cat->is_active)
                                        <span class="b b-ok">Aktif</span>
                                    @else
                                        <span class="b b-off">Nonaktif</span>
                                    @endif
                                </td>
                                <td style="text-align:right">
                                    <div class="row-actions">
                                        <form method="POST" action="{{ route('admin.categories.toggle-status', $cat) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-ghost btn-sm">
                                                {{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        @if(($cat->incidents_count ?? 0) === 0)
                                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Hapus kategori {{ $cat->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty">Belum ada kategori insiden di database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <section class="glass card">
        <div class="card-h">
            <h2><i data-lucide="plus-circle"></i>Tambah Kategori Baru</h2>
        </div>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="field">
                <label class="label text-xs" for="cat_name">Nama Kategori <span class="req">*</span></label>
                <input type="text" id="cat_name" name="name" value="{{ old('name') }}" class="input sm" placeholder="Contoh: Serangan DDoS" required>
            </div>
            <div class="field">
                <label class="label text-xs" for="cat_desc">Deskripsi Kategori <span class="req">*</span></label>
                <textarea id="cat_desc" name="description" rows="4" class="textarea text-xs" placeholder="Jelaskan ruang lingkup kategori insiden ini..." required>{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <i data-lucide="check"></i>
                <span>Simpan Kategori</span>
            </button>
        </form>
    </section>
</div>
@endsection
