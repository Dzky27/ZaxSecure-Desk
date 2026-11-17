<div class="stat-card">
    <div class="stat-head">
        <h3>{{ $label ?? 'Statistik' }}</h3>
        <span class="stat-icon">
            <i data-lucide="{{ $icon ?? 'shield-check' }}"></i>
        </span>
    </div>
    <div class="stat-value">{{ $value ?? '0' }}</div>
    @if(!empty($subtext))
        <div class="stat-sub">{{ $subtext }}</div>
    @endif
</div>
