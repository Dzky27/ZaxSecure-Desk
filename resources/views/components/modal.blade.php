@props(['title', 'body', 'footer' => null, 'visible' => true])

@if($visible)
<div class="modal-overlay" id="{{ $overlayId ?? 'modal-overlay' }}">
    <div class="modal" role="dialog" aria-modal="true">
        <div class="modal-head">
            <h3>{{ $title ?? 'Modal' }}</h3>
            <button class="modal-close" data-modal-close aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-body">
            {!! $body !!}
        </div>
        @if($footer)
        <div class="modal-foot">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>
@endif
