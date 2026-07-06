<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SIMKLINIK'))</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.css') }}" />
    @stack('styles')
</head>
<body class="bg-body-tertiary">
    <div class="container py-4">
        @yield('content')
    </div>
    <script src="{{ asset('assets/js/adminlte.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
