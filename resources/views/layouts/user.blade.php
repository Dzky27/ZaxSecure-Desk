@extends('layouts.app')

@section('content')
<a href="#content" class="sr-only">Lewati ke konten utama</a>
<aside id="sidebar" class="sidebar" aria-label="Navigasi Utama">
    @include('components.sidebar')
</aside>
<div class="scrim" id="scrim"></div>
<div class="main">
    <header class="topbar" id="topbar">
        @include('components.topbar')
    </header>
    <main class="page" id="content">
        @yield('page-content')
    </main>
</div>
<div id="toasts" class="toast-wrap" aria-live="polite"></div>
@endsection
