@extends('layouts.admin')

@section('title', 'Laporan & Analitik — ZaxSecure Desk')

@section('page-content')
<div class="page-head">
    <div>
        <h1>Laporan & Analitik Keamanan</h1>
        <p class="sub">Evaluasi kinerja penanganan insiden, tingkat penyelesaian (SLA), dan distribusi ancaman dari database aktual.</p>
    </div>
    <div class="actions">
        <button type="button" onclick="window.print()" class="btn btn-ghost btn-sm">
            <i data-lucide="printer"></i>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>
</div>

<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    @include('components.stat-card', [
        'label' => 'Total Laporan Tercatat',
        'value' => (string) ($total ?? 0),
        'icon' => 'file-text',
        'subtext' => 'Akumulasi seluruh tiket'
    ])
    @include('components.stat-card', [
        'label' => 'Tingkat Penyelesaian',
        'value' => ($resolutionRate ?? 0) . '%',
        'icon' => 'check-circle-2',
        'subtext' => ($resolved ?? 0) . ' insiden selesai ditangani'
    ])
    @include('components.stat-card', [
        'label' => 'Sedang Ditangani',
        'value' => (string) ($inProgress ?? 0),
        'icon' => 'activity',
        'subtext' => 'Dalam investigasi & proses'
    ])
    @include('components.stat-card', [
        'label' => 'Insiden Prioritas Kritis',
        'value' => (string) ($critical ?? 0),
        'icon' => 'flame',
        'subtext' => 'Memerlukan eskalasi segera'
    ])
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Category Breakdown -->
    <section class="glass card">
        <div class="card-h">
            <h2><i data-lucide="tags"></i>Rekapitulasi per Kategori Ancaman</h2>
        </div>
        <div class="space-y-3">
            @forelse($categoryBreakdown ?? [] as $cat)
                @php
                    $pct = ($total ?? 0) > 0 ? round(($cat->incidents_count / $total) * 100) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-medium text-white">{{ $cat->name }}</span>
                        <span class="mono text-slate-300">{{ $cat->incidents_count }} tiket ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full rounded-full bg-red-500" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400">Belum ada data kategori.</p>
            @endforelse
        </div>
    </section>

    <!-- Priority Breakdown -->
    <section class="glass card">
        <div class="card-h">
            <h2><i data-lucide="bar-chart-2"></i>Distribusi Berdasarkan Tingkat Prioritas</h2>
        </div>
        <div class="rows text-sm">
            <div>
                <span><span class="b b-pri-critical mr-2">Kritis</span> Ancaman Berdampak Tinggi / Sistemik</span>
                <strong class="mono text-white">{{ $priorityBreakdown['critical'] ?? 0 }} laporan</strong>
            </div>
            <div>
                <span><span class="b b-pri-high mr-2">Tinggi</span> Eksploitasi Aktif / Akses Tidak Sah</span>
                <strong class="mono text-white">{{ $priorityBreakdown['high'] ?? 0 }} laporan</strong>
            </div>
            <div>
                <span><span class="b b-pri-medium mr-2">Sedang</span> Indikasi Mencurigakan Terverifikasi</span>
                <strong class="mono text-white">{{ $priorityBreakdown['medium'] ?? 0 }} laporan</strong>
            </div>
            <div>
                <span><span class="b b-pri-low mr-2">Rendah</span> Temuan Awal / Peringatan Preventif</span>
                <strong class="mono text-white">{{ $priorityBreakdown['low'] ?? 0 }} laporan</strong>
            </div>
        </div>
    </section>
</div>

<section class="glass card p-0 overflow-hidden">
    <div class="p-5 border-b border-white/10 flex items-center justify-between">
        <h2 class="font-semibold text-white text-sm flex items-center gap-2">
            <i data-lucide="table" class="text-red-400"></i>
            Daftar Lengkap Laporan untuk Audit
        </h2>
        <span class="text-xs text-slate-400">Menampilkan {{ isset($incidents) ? $incidents->count() : 0 }} data</span>
    </div>
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Nomor Tiket</th>
                    <th>Judul Insiden</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Pelapor</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents ?? [] as $inc)
                    <tr>
                        <td class="mono">
                            <a href="{{ route('admin.incidents.show', $inc) }}" class="link">{{ $inc->ticket_number }}</a>
                        </td>
                        <td>{{ $inc->title }}</td>
                        <td class="text-xs">{{ $inc->category->name ?? '—' }}</td>
                        <td>@include('components.priority-badge', ['priority' => $inc->normalized_priority, 'text' => $inc->priority_badge])</td>
                        <td>@include('components.status-badge', ['status' => $inc->normalized_status, 'text' => $inc->status_badge])</td>
                        <td class="text-xs">{{ $inc->reporter->name ?? '—' }}</td>
                        <td class="mono text-xs">{{ $inc->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">Belum ada laporan insiden yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
