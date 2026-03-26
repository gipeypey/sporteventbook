<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SportEventBook') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a1a1a1; }
        
        /* Smooth animations */
        * { transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease; }
        
        /* Fade in animation */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
        .animate-fade-in-delay { animation: fadeIn 0.6s ease-out 0.2s forwards; opacity: 0; }
        .animate-fade-in-delay-2 { animation: fadeIn 0.6s ease-out 0.4s forwards; opacity: 0; }
        
        /* Smooth scroll padding for fixed header */
        html { scroll-padding-top: 100px; }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased">

    {{-- Navbar --}}
    @yield('navbar')

    {{-- Content --}}
    @yield('content')

    {{-- Footer --}}
    @yield('footer')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="{{ asset('assets/js/home.js') }}" defer></script>
    <script src="{{ asset('assets/js/promo-code.js') }}" defer></script>
    <script src="{{ asset('assets/js/e-ticket.js') }}" defer></script>

    @yield('scripts')
</body>

</html>