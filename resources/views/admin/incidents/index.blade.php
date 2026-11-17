@extends('layouts.admin')

@section('title', 'Manajemen Insiden — ZaxSecure Desk')

@section('page-content')
<div class="page-head">
    <div>
        <h1>Manajemen Insiden</h1>
        <p class="sub">Kelola seluruh laporan insiden keamanan siber, triase prioritas, penugasan analis, dan pembaruan status.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.incidents.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i>
            <span>Buat Insiden Baru</span>
        </a>
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

<section class="glass card mb-6">
    <form method="GET" action="{{ route('admin.incidents.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
        <div class="lg:col-span-2">
            <label class="label text-xs mb-1" for="search">Pencarian Cepat</label>
            <input type="text" id="search" name="search" placeholder="Cari nomor tiket atau judul insiden..." value="{{ request('search') }}" class="input sm">
        </div>
        <div>
            <label class="label text-xs mb-1" for="status">Status Penanganan</label>
            <select id="status" name="status" class="select sm">
                <option value="">Semua Status</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label text-xs mb-1" for="priority">Tingkat Prioritas</label>
            <select id="priority" name="priority" class="select sm">
                <option value="">Semua Prioritas</option>
                @foreach($priorities as $value => $label)
                    <option value="{{ $value }}" {{ request('priority') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <div class="flex-1">
                <label class="label text-xs mb-1" for="category_id">Kategori</label>
                <select id="category_id" name="category_id" class="select sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm self-end h-[35px]" title="Terapkan Filter">
                <i data-lucide="filter"></i>
                <span>Filter</span>
            </button>
            @if(request()->hasAny(['search', 'status', 'priority', 'category_id']))
                <a href="{{ route('admin.incidents.index') }}" class="btn btn-ghost btn-sm self-end h-[35px]" title="Reset Filter">
                    <i data-lucide="rotate-ccw"></i>
                </a>
            @endif
        </div>
    </form>
</section>

<section class="glass card p-0 overflow-hidden">
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Tiket</th>
                    <th>Judul & Pelapor</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Penanggung Jawab</th>
                    <th>Tanggal</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents as $index => $incident)
                    <tr>
                        <td class="muted text-xs">{{ $incidents->firstItem() + $index }}</td>
                        <td class="mono">
                            <a href="{{ route('admin.incidents.show', $incident) }}" class="link">{{ $incident->ticket_number }}</a>
                        </td>
                        <td>
                            <a href="{{ route('admin.incidents.show', $incident) }}" class="ttl block hover:underline" title="{{ $incident->title }}">{{ $incident->title }}</a>
                            <span class="text-xs text-slate-400">Oleh: {{ $incident->reporter->name ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="text-xs px-2.5 py-1 rounded-md bg-white/[0.04] border border-white/10 text-slate-300">
                                {{ $incident->category->name ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @include('components.priority-badge', ['priority' => $incident->normalized_priority, 'text' => $incident->priority_badge])
                        </td>
                        <td>
                            @include('components.status-badge', ['status' => $incident->normalized_status, 'text' => $incident->status_badge])
                        </td>
                        <td class="text-xs">
                            @if($incident->assignedAdmin)
                                <span class="text-slate-200 font-medium">{{ $incident->assignedAdmin->name }}</span>
                            @else
                                <span class="text-slate-500 italic">Belum ditugaskan</span>
                            @endif
                        </td>
                        <td class="muted text-xs">{{ $incident->created_at->format('d M Y') }}</td>
                        <td style="text-align:right">
                            <div class="row-actions">
                                <a href="{{ route('admin.incidents.show', $incident) }}" class="btn btn-ghost btn-sm">Detail</a>
                                <a href="{{ route('admin.incidents.edit', $incident) }}" class="btn btn-ghost btn-sm">Ubah</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty">
                            <div class="space-y-2">
                                <p class="text-slate-300 font-medium">Tidak ada laporan insiden yang ditemukan.</p>
                                <p class="text-xs text-slate-500">Ubah parameter pencarian atau buat laporan insiden baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($incidents->hasPages())
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-5 py-4 border-t border-white/10 text-xs text-slate-400">
            <div>
                Menampilkan <strong>{{ $incidents->firstItem() ?? 0 }}</strong> sampai <strong>{{ $incidents->lastItem() ?? 0 }}</strong> dari <strong>{{ $incidents->total() }}</strong> insiden
            </div>
            <div>
                {{ $incidents->links() }}
            </div>
        </div>
    @endif
</section>
@endsection
