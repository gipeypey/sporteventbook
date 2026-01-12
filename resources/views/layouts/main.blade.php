<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS -->
    <link href="{{ asset('dist/output.css') }}" rel="stylesheet">
    <!-- Swiper CSS (UMD) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
</head>

<body class="bg-white text-gray-900 antialiased">

    {{-- Konten halaman --}}
    @yield('content')


    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="{{ asset('assets/js/home.js') }}" defer></script>
    <script src="{{ asset('assets/js/payment.js') }}" defer></script>
    <script src="{{ asset('assets/js/e-ticket.js') }}" defer></script>

    {{-- Scripts khusus halaman --}}
    @yield('scripts')
</body>

</html>
