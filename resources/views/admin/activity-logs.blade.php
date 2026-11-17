@extends('layouts.admin')

@section('title', 'Jejak Audit Aktivitas — ZaxSecure Desk')

@section('page-content')
<div class="page-head">
    <div>
        <h1>Jejak Audit Aktivitas & Peristiwa Keamanan</h1>
        <p class="sub">Rekam jejak autentikasi, perubahan status insiden, unggahan bukti, dan peringatan keamanan sistem.</p>
    </div>
</div>

<section class="glass card mb-6">
    <form method="GET" action="{{ route('admin.activity-logs') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan jenis aksi, alamat IP, atau tipe objek..." class="input sm">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">
            <i data-lucide="search"></i>
            <span>Filter Log</span>
        </button>
        @if(request()->filled('search'))
            <a href="{{ route('admin.activity-logs') }}" class="btn btn-ghost btn-sm">Reset</a>
        @endif
    </form>
</section>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
    <section class="glass card xl:col-span-2 p-0 overflow-hidden">
        <div class="p-5 border-b border-white/10">
            <h2 class="font-semibold text-white text-sm flex items-center gap-2">
                <i data-lucide="scroll-text" class="text-red-400"></i>
                Log Aktivitas Pengguna & Administrator
            </h2>
        </div>
        <div class="tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pelaku</th>
                        <th>Aksi</th>
                        <th>Objek</th>
                        <th>Alamat IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="mono text-xs">{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                            <td>
                                <span class="font-medium text-white">{{ $log->user->name ?? 'Sistem' }}</span>
                            </td>
                            <td>
                                <span class="mono text-xs px-2 py-0.5 rounded bg-white/[0.06] text-red-300">{{ $log->action }}</span>
                            </td>
                            <td class="text-xs text-slate-300">
                                {{ $log->subject_type ? $log->subject_type . ' #' . $log->subject_id : '—' }}
                            </td>
                            <td class="mono text-xs text-slate-400">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">Belum ada catatan aktivitas di dalam log audit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($logs, 'hasPages') && $logs->hasPages())
            <div class="px-5 py-4 border-t border-white/10 text-xs text-slate-400 flex items-center justify-between">
                <span>Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log</span>
                <div>{{ $logs->links() }}</div>
            </div>
        @endif
    </section>

    <section class="glass card">
        <div class="card-h">
            <h2><i data-lucide="shield-alert"></i>Peristiwa Keamanan (Security Events)</h2>
        </div>
        <div class="space-y-3">
            @forelse($securityEvents ?? [] as $ev)
                <div class="p-3.5 rounded-xl bg-[#090d16] border border-white/10 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="mono font-semibold text-white">{{ $ev->event_type }}</span>
                        @include('components.priority-badge', ['priority' => $ev->severity])
                    </div>
                    <p class="text-slate-300">{{ $ev->description }}</p>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 mono pt-1">
                        <span>IP: {{ $ev->ip_address ?? '-' }}</span>
                        <span>{{ $ev->created_at?->format('d M H:i') }}</span>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-xs text-slate-400">
                    Tidak ada peringatan peristiwa keamanan mencurigakan.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
