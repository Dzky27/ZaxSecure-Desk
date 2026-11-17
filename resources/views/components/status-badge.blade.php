@php
    $raw = strtolower(trim((string) ($status ?? $type ?? 'pending')));
    $norm = match ($raw) {
        'pending', 'open', 'menunggu' => 'pending',
        'investigating', 'investigasi' => 'investigating',
        'in_progress', 'diproses' => 'in_progress',
        'resolved', 'selesai' => 'resolved',
        'rejected', 'closed', 'ditolak' => 'rejected',
        default => 'pending',
    };
    $label = $text ?? match ($norm) {
        'pending' => 'Menunggu',
        'investigating' => 'Investigasi',
        'in_progress' => 'Diproses',
        'resolved' => 'Selesai',
        'rejected' => 'Ditolak',
        default => 'Menunggu',
    };
@endphp
<span class="b b-st-{{ $norm }}">{{ $label }}</span>
