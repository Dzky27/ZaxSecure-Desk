<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ZaxSecure Desk — Platform Manajemen Insiden Siber')</title>
    <meta name="description" content="ZaxSecure Desk — Platform Operasional Manajemen Insiden Keamanan Siber Terpadu.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/favicon.svg') }}">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="antialiased selection:bg-red-600 selection:text-white">
    @yield('content')
    <script src="https://unpkg.com/lucide@0.441.0/dist/umd/lucide.min.js" defer></script>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                try { window.lucide.createIcons(); } catch (e) {}
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
