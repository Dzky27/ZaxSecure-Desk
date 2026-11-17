@php
    $raw = strtolower(trim((string) ($priority ?? $type ?? 'low')));
    $norm = match ($raw) {
        'critical', 'kritis' => 'critical',
        'high', 'tinggi' => 'high',
        'medium', 'sedang' => 'medium',
        default => 'low',
    };
    $label = $text ?? match ($norm) {
        'critical' => 'Kritis',
        'high' => 'Tinggi',
        'medium' => 'Sedang',
        default => 'Rendah',
    };
@endphp
<span class="b b-pri-{{ $norm }}">{{ $label }}</span>
