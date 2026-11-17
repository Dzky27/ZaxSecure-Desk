@props(['message', 'type' => 'info', 'duration' => 4000])

<div class="toast toast-{{ $type ?? 'info' }}" role="alert">
    <div class="toast-icon">
        <i data-lucide="info"></i>
    </div>
    <div class="toast-msg">{{ $message }}</div>
    <button class="toast-close" data-toast-close aria-label="Tutup">&times;</button>
</div>
