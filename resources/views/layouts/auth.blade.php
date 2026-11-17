@extends('layouts.app')

@section('content')
<main class="auth">
    <div style="width:100%">
        @yield('auth-card')
    </div>
</main>
<div id="toasts" class="toast-wrap" aria-live="polite"></div>
@endsection
