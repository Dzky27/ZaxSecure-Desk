@php
    $currentUser = auth()->user();
    $isAdminArea = request()->routeIs('admin.*') || ($currentUser && $currentUser->isAdmin());
    $userName = $currentUser?->name ?? ($isAdminArea ? 'Administrator' : 'Pengguna');
    $userEmail = $currentUser?->email ?? '';
    $initials = collect(explode(' ', $userName))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp

<div class="sidebar-header">
    @include('components.logo', ['compact' => true, 'size' => 34])
    <div>
        @if($isAdminArea)
            <span class="sidebar-role-badge">
                <i data-lucide="shield-alert" style="width:12px;height:12px"></i>
                Pusat Operasi Admin
            </span>
        @else
            <span class="sidebar-role-badge role-user">
                <i data-lucide="user-check" style="width:12px;height:12px"></i>
                Portal Pelapor Insiden
            </span>
        @endif
    </div>
</div>

<ul class="sidebar-nav">
    @if($isAdminArea)
        <li class="nav-section">Operasional Utama</li>
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Ringkasan Utama</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.incidents.index') }}" class="{{ request()->routeIs('admin.incidents.*') ? 'active' : '' }}">
                <i data-lucide="shield-alert"></i>
                <span>Manajemen Insiden</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i data-lucide="tags"></i>
                <span>Kategori Insiden</span>
            </a>
        </li>

        <li class="nav-section">Tata Kelola & Audit</li>
        <li>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i data-lucide="users"></i>
                <span>Manajemen Pengguna</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3"></i>
                <span>Laporan & Analitik</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.activity-logs') }}" class="{{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}">
                <i data-lucide="scroll-text"></i>
                <span>Jejak Audit Aktivitas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i data-lucide="settings"></i>
                <span>Pengaturan Sistem</span>
            </a>
        </li>
    @else
        <li class="nav-section">Menu Pelaporan</li>
        <li>
            <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Ringkasan Saya</span>
            </a>
        </li>
        <li>
            <a href="{{ route('user.incidents.create') }}" class="{{ request()->routeIs('user.incidents.create') ? 'active' : '' }}">
                <i data-lucide="file-plus-2"></i>
                <span>Buat Laporan Baru</span>
            </a>
        </li>
        <li>
            <a href="{{ route('user.incidents.index') }}" class="{{ request()->routeIs('user.incidents.index') || request()->routeIs('user.incidents.show') || request()->routeIs('user.incidents.edit') ? 'active' : '' }}">
                <i data-lucide="folder-kanban"></i>
                <span>Laporan Insiden Saya</span>
            </a>
        </li>

        <li class="nav-section">Akun</li>
        <li>
            <a href="{{ route('user.profile') }}" class="{{ request()->routeIs('user.profile*') ? 'active' : '' }}">
                <i data-lucide="user-cog"></i>
                <span>Profil & Keamanan</span>
            </a>
        </li>
    @endif
</ul>

<div class="sidebar-footer">
    <div class="sidebar-user-card">
        <div class="avatar sm">{{ strtoupper($initials ?: 'U') }}</div>
        <div class="sidebar-user-info">
            <b title="{{ $userName }}">{{ $userName }}</b>
            <small title="{{ $userEmail }}">{{ $userEmail ?: ($isAdminArea ? 'Administrator' : 'Pengguna Terdaftar') }}</small>
        </div>
    </div>
    <form method="POST" action="{{ route('auth.logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm btn-block">
            <i data-lucide="log-out"></i>
            <span>Keluar dari Sesi</span>
        </button>
    </form>
</div>
