@props(['total' => 0, 'perPage' => 10, 'currentPage' => 1, 'url' => null])

@php
$totalPages = max(1, (int) ceil(($total ?? 0) / max(1, (int) $perPage)));
$range = 3;
$start = max(1, $currentPage - $range);
$end = min($totalPages, $currentPage + $range);
$pages = range($start, $end);
@endphp

<nav class="pager" aria-label="Halaman">
    <ul class="pager-list">
        @if($currentPage > 1)
        <li><a href="{{ $url ?? '' }}={{ $currentPage - 1 }}" aria-label="Halaman sebelumnya">&laquo;</a></li>
        @else
        <li class="disabled"><span aria-hidden="true">&laquo;</span></li>
        @endif

        @foreach($pages as $p)
        @if($p == $currentPage)
        <li class="active"><span aria-current="page">{{ $p }}</span></li>
        @elseif($p == 1)
        <li><a href="{{ $url ?? '' }}={{ $p }}">{{ $p }}</a></li>
        @elseif($p > 1)
        <li class="dots"><span aria-hidden="true">&hellip;</span></li>
        @endif
        @endforeach

        @if($currentPage < $totalPages)
        <li><a href="{{ $url ?? '' }}={{ $currentPage + 1 }}" aria-label="Halaman berikutnya">&raquo;</a></li>
        @else
        <li class="disabled"><span aria-hidden="true">&raquo;</span></li>
        @endif
    </ul>
</nav>
