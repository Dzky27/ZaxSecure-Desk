@extends('layouts.user')

@section('title', 'Ringkasan Saya — ZaxSecure Desk')

@section('page-content')
<div class="page-head">
    <div>
        <h1>Ringkasan Laporan Saya</h1>
        <p class="sub">Selamat datang kembali, <span class="text-red font-semibold">{{ $user?->name ?? 'Pelapor' }}</span>. Pantau perkembangan laporan insiden keamanan Anda di sini.</p>
    </div>
    <div class="actions">
        <a class="btn btn-ghost btn-sm" href="{{ route('user.incidents.index') }}">
            <i data-lucide="folder-kanban"></i>
            <span>Semua Laporan Saya</span>
        </a>
        <a class="btn btn-primary btn-sm" href="{{ route('user.incidents.create') }}">
            <i data-lucide="file-plus-2"></i>
            <span>Buat Laporan Baru</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" role="status">
        <i data-lucide="check-circle-2"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

<!-- User KPI Cards (Preserves test assertions: Total Laporan, Open, Diproses, Selesai) -->
<div id="stats" class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    @include('components.stat-card', [
        'label' => 'Total Laporan',
        'value' => (string) ($stats['total_incidents'] ?? 0),
        'icon' => 'file-text',
        'subtext' => 'Laporan yang Anda kirim'
    ])
    @include('components.stat-card', [
        'label' => 'Open',
        'value' => (string) ($stats['open'] ?? 0),
        'icon' => 'alert-circle',
        'subtext' => 'Laporan aktif / terbuka'
    ])
    @include('components.stat-card', [
        'label' => 'Diproses',
        'value' => (string) ($stats['processing'] ?? 0),
        'icon' => 'activity',
        'subtext' => 'Sedang ditangani tim SOC'
    ])
    @include('components.stat-card', [
        'label' => 'Selesai',
        'value' => (string) ($stats['resolved'] ?? 0),
        'icon' => 'check-circle-2',
        'subtext' => 'Penanganan tuntas'
    ])
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
    <section class="glass card xl:col-span-2">
        <div class="card-h">
            <h2><i data-lucide="folder-kanban"></i>Laporan Insiden Terbaru Saya</h2>
            <a class="text-red text-xs font-semibold hover:underline" href="{{ route('user.incidents.index') }}">Lihat Semua &rarr;</a>
        </div>
        <div class="tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Nomor Tiket</th>
                        <th>Judul Laporan</th>
                        <th>Kategori</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="recent">
                    @forelse($recent as $item)
                        <tr>
                            <td class="mono">
                                <a class="link" href="{{ route('user.incidents.show', $item['model_id']) }}">{{ $item['id'] }}</a>
                            </td>
                            <td>
                                <div class="ttl" title="{{ $item['title'] }}">{{ $item['title'] }}</div>
                            </td>
                            <td class="text-xs">{{ $item['category'] ?? '-' }}</td>
                            <td>
                                @include('components.priority-badge', ['priority' => $item['priority'], 'text' => $item['priority_label']])
                            </td>
                            <td>
                                @include('components.status-badge', ['status' => $item['status'], 'text' => $item['status_label']])
                            </td>
                            <td class="muted text-xs">{{ $item['date'] ?? '-' }}</td>
                            <td style="text-align:right">
                                <div class="row-actions">
                                    <a class="btn btn-ghost btn-sm" href="{{ route('user.incidents.show', $item['model_id']) }}">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">
                                <div class="space-y-2">
                                    <p>Anda belum membuat laporan insiden keamanan.</p>
                                    <a href="{{ route('user.incidents.create') }}" class="btn btn-primary btn-sm">Buat Laporan Pertama</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="glass card flex flex-col justify-between">
        <div>
            <div class="card-h">
                <h2><i data-lucide="bell-ring"></i>Pembaruan Tindak Lanjut</h2>
            </div>
            <div id="updates" class="space-y-3">
                @forelse($recentUpdates as $u)
                    <div class="p-3 rounded-lg bg-white/[0.03] border border-white/10 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-white">{{ $u->user ? $u->user->name : 'Tim Keamanan SOC' }}</span>
                            <span class="mono text-[11px] text-slate-400">{{ $u->created_at ? $u->created_at->format('d M Y H:i') : '-' }}</span>
                        </div>
                        @if($u->incident)
                            <div class="mono text-[11px] text-red-400">Tiket: {{ $u->incident->ticket_number }}</div>
                        @endif
                        <p class="text-slate-300">{{ $u->message ?? 'Status laporan diperbarui.' }}</p>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        Belum ada pembaruan publik untuk laporan Anda.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-white/10">
            <div class="p-3 rounded-lg bg-red-500/10 border border-red-500/25 text-xs text-slate-300 space-y-1.5">
                <div class="font-semibold text-white flex items-center gap-1.5">
                    <i data-lucide="info" class="text-red-400" style="width:14px;height:14px"></i>
                    Panduan Pelaporan Cepat
                </div>
                <p>Lampirkan tangkapan layar atau berkas bukti pada halaman detail laporan agar tim SOC dapat memverifikasi insiden lebih cepat.</p>
            </div>
        </div>
    </section>
</div>
@endsection
