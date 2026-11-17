@php
    $currentUser = auth()->user();
    $isAdminArea = request()->routeIs('admin.*') || ($currentUser && $currentUser->isAdmin());
    $searchRoute = $isAdminArea ? route('admin.incidents.index') : route('user.incidents.index');
    $userName = $currentUser?->name ?? ($isAdminArea ? 'Administrator' : 'Pengguna');
    $initials = collect(explode(' ', $userName))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp

<div class="topbar-inner">
    <div class="topbar-left">
        <button type="button" class="icon-btn sidebar-toggler" id="sidebarToggleBtn" data-sidebar-toggler aria-label="Buka menu navigasi">
            <i data-lucide="menu"></i>
        </button>
        <form method="GET" action="{{ $searchRoute }}" class="search" role="search">
            <i data-lucide="search"></i>
            <input
                class="input sm"
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="{{ $isAdminArea ? 'Cari nomor tiket atau judul insiden...' : 'Cari laporan insiden saya...' }}"
                aria-label="Cari insiden"
            >
        </form>
    </div>

    <div class="topbar-right">
        @if($isAdminArea)
            <a href="{{ route('admin.incidents.create') }}" class="btn btn-primary btn-sm">
                <i data-lucide="plus"></i>
                <span class="hidden sm:inline">Catat Insiden</span>
            </a>
        @else
            <a href="{{ route('user.incidents.create') }}" class="btn btn-primary btn-sm">
                <i data-lucide="file-plus-2"></i>
                <span class="hidden sm:inline">Lapor Insiden</span>
            </a>
        @endif

        <a href="{{ $isAdminArea ? route('admin.settings') : route('user.profile') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg border border-white/10 bg-white/[0.03] hover:bg-white/[0.07] transition-colors" title="Pengaturan Profil">
            <div class="avatar sm">{{ strtoupper($initials ?: 'U') }}</div>
            <div class="hidden md:block text-left">
                <div class="text-xs font-semibold text-white leading-tight">{{ $userName }}</div>
                <div class="text-[11px] text-slate-400 leading-tight">{{ $isAdminArea ? 'Administrator SOC' : 'Pelapor' }}</div>
            </div>
        </a>
    </div>
</div>
