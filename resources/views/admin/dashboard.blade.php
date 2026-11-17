@extends('layouts.admin')

@section('title', 'Ringkasan Utama SOC — ZaxSecure Desk')

@section('page-content')
<div class="page-head">
    <div>
        <h1>Ringkasan Operasional SOC</h1>
        <p class="sub">Selamat datang kembali, <span class="text-red font-semibold">{{ $user?->name ?? 'Administrator' }}</span>. Berikut status penanganan insiden keamanan terkini.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.incidents.index') }}" class="btn btn-ghost btn-sm">
            <i data-lucide="list-filter"></i>
            <span>Daftar Insiden</span>
        </a>
        <a href="{{ route('admin.incidents.create') }}" class="btn btn-primary btn-sm">
            <i data-lucide="plus"></i>
            <span>Buat Laporan Insiden</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" role="status">
        <i data-lucide="check-circle-2"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

<!-- Primary KPI Cards (Preserves test assertions: Total Insiden, Pending, Diproses, Selesai, Kategori) -->
<div id="stats" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
    @include('components.stat-card', [
        'label' => 'Total Insiden',
        'value' => (string) ($stats['total_incidents'] ?? 0),
        'icon' => 'shield-alert',
        'subtext' => 'Seluruh tiket tercatat'
    ])
    @include('components.stat-card', [
        'label' => 'Pending',
        'value' => (string) ($stats['pending'] ?? 0),
        'icon' => 'clock',
        'subtext' => 'Menunggu triase awal'
    ])
    @include('components.stat-card', [
        'label' => 'Diproses',
        'value' => (string) ($stats['processing'] ?? 0),
        'icon' => 'activity',
        'subtext' => 'Dalam investigasi & mitigasi'
    ])
    @include('components.stat-card', [
        'label' => 'Selesai',
        'value' => (string) ($stats['resolved'] ?? 0),
        'icon' => 'check-circle-2',
        'subtext' => 'Insiden berhasil ditangani'
    ])
    @include('components.stat-card', [
        'label' => 'Kategori',
        'value' => (string) ($stats['categories'] ?? 0),
        'icon' => 'tags',
        'subtext' => 'Klasifikasi ancaman aktif'
    ])
</div>

<!-- Charts & Priority Overview -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">
    <section class="glass card xl:col-span-2">
        <div class="card-h">
            <h2><i data-lucide="bar-chart-3"></i>Tren Laporan Insiden (6 Bulan Terakhir)</h2>
            <span class="text-xs text-slate-400">Data Aktual Database</span>
        </div>
        <div class="chart-box">
            <canvas id="trendChart" aria-label="Grafik tren laporan insiden" role="img"></canvas>
        </div>
    </section>

    <section class="glass card flex flex-col justify-between">
        <div>
            <div class="card-h">
                <h2><i data-lucide="flame"></i>Distribusi Prioritas Ancaman</h2>
            </div>
            @php
                $totalPri = max(1, array_sum($prioritySummary ?? []));
                $priItems = [
                    ['key' => 'critical', 'label' => 'Kritis (Critical)', 'count' => $prioritySummary['critical'] ?? 0, 'color' => '#a855f7', 'badge' => 'b-pri-critical'],
                    ['key' => 'high', 'label' => 'Tinggi (High)', 'count' => $prioritySummary['high'] ?? 0, 'color' => '#ef4444', 'badge' => 'b-pri-high'],
                    ['key' => 'medium', 'label' => 'Sedang (Medium)', 'count' => $prioritySummary['medium'] ?? 0, 'color' => '#f59e0b', 'badge' => 'b-pri-medium'],
                    ['key' => 'low', 'label' => 'Rendah (Low)', 'count' => $prioritySummary['low'] ?? 0, 'color' => '#64748b', 'badge' => 'b-pri-low'],
                ];
            @endphp
            <div class="space-y-4 mt-2">
                @foreach($priItems as $pItem)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="b {{ $pItem['badge'] }}">{{ $pItem['label'] }}</span>
                            <span class="font-mono font-semibold text-white">{{ $pItem['count'] }} tiket</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-white/10 overflow-hidden">
                            <div class="h-full rounded-full" style="width: {{ round(($pItem['count'] / $totalPri) * 100) }}%; background: {{ $pItem['color'] }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
            <span>Pintasan Triase Cepat:</span>
            <a href="{{ route('admin.incidents.index', ['priority' => 'critical']) }}" class="text-red hover:underline font-medium">Filter Insiden Kritis &rarr;</a>
        </div>
    </section>
</div>

<!-- Recent Incidents Table & Recent Audit Activity -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
    <section class="glass card xl:col-span-2">
        <div class="card-h">
            <h2><i data-lucide="file-text"></i>Laporan Insiden Terbaru</h2>
            <a class="text-red text-xs font-semibold hover:underline" href="{{ route('admin.incidents.index') }}">Lihat Semua Insiden &rarr;</a>
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
                        <th>Penanggung Jawab</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="recent">
                    @forelse($recent as $item)
                        <tr>
                            <td class="mono">
                                <a class="link" href="{{ route('admin.incidents.show', $item['model_id']) }}">{{ $item['id'] }}</a>
                            </td>
                            <td>
                                <div class="ttl" title="{{ $item['title'] }}">{{ $item['title'] }}</div>
                                <small class="muted text-xs">Pelapor: {{ $item['reporter'] }} • {{ $item['date'] ?? '-' }}</small>
                            </td>
                            <td>{{ $item['category'] }}</td>
                            <td>
                                @include('components.priority-badge', ['priority' => $item['priority'], 'text' => $item['priority_label']])
                            </td>
                            <td>
                                @include('components.status-badge', ['status' => $item['status'], 'text' => $item['status_label']])
                            </td>
                            <td class="text-xs">{{ $item['assigned'] }}</td>
                            <td style="text-align:right">
                                <div class="row-actions">
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.incidents.show', $item['model_id']) }}">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">
                                Belum ada laporan insiden yang tercatat di dalam database.
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
                <h2><i data-lucide="history"></i>Aktivitas Sistem Terbaru</h2>
                <a href="{{ route('admin.activity-logs') }}" class="text-xs text-red hover:underline">Log Audit</a>
            </div>
            <div class="space-y-3">
                @forelse($recentActivities ?? [] as $act)
                    <div class="p-3 rounded-lg bg-white/[0.03] border border-white/10 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-white">{{ $act->user?->name ?? 'Sistem' }}</span>
                            <span class="mono text-[11px] text-slate-400">{{ $act->created_at?->format('d M H:i') }}</span>
                        </div>
                        <div class="text-slate-300 font-mono text-[11.5px]">{{ $act->action }}</div>
                        @if($act->ip_address)
                            <div class="text-[11px] text-slate-500">IP: {{ $act->ip_address }}</div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        Belum ada aktivitas audit terbaru.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-white/10 grid grid-cols-2 gap-2">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost btn-sm">
                <i data-lucide="tags"></i>
                <span>Kelola Kategori</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="btn btn-ghost btn-sm">
                <i data-lucide="pie-chart"></i>
                <span>Buka Analitik</span>
            </a>
        </div>
    </section>
</div>

<script>
window.DASHBOARD_DATA = {
    trend: {
        labels: @json($trendLabels),
        values: @json($trendValues)
    },
    category: {
        labels: @json($categoryLabels),
        values: @json($categoryValues)
    }
};
</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
@endpush
