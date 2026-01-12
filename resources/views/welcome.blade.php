@extends('layouts.main')

@section('content')
<div class="bg-white min-h-screen">
    <div class="relative flex flex-col w-full max-w-[640px] min-h-screen mx-auto bg-white overflow-x-hidden">

        <section class="flex flex-col items-center justify-center text-center px-6 pt-24 pb-12">
            <div class="mb-6">
                <img src="{{ asset('assets/images/medal.png') }}" alt="Logo" class="w-16 h-16 mx-auto" />
            </div>
            <h1 class="text-2xl font-semibold text-[#111223] mb-2">Welcome to {{ config('app.name') }}</h1>
            <p class="text-[#9BA4A6] max-w-md">
                Temukan dan ikuti berbagai event olahraga. Raih hadiah dan tingkatkan performa kamu.
            </p>

            <div class="mt-8 flex items-center gap-3">
                <a href="{{ route('home') }}"
                    class="bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] text-[#18192B] rounded-full px-6 py-2 font-bold">
                    Mulai Jelajah
                </a>
                <a href="{{ route('browse-events') }}"
                    class="border border-gray-200 rounded-full px-6 py-2 font-semibold text-[#111223] hover:border-[#FF7A00] transition-colors">
                    Lihat Event
                </a>
            </div>
        </section>

        <section class="px-4 pb-16">
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-2xl border border-gray-200 p-4 flex items-center gap-3 hover:border-[#FF7A00] transition-colors">
                    <img src="{{ asset('assets/icons/crown.svg') }}" class="w-6 h-6" alt="Kategori" />
                    <div class="text-left">
                        <div class="text-sm font-semibold text-[#18192B]">Kategori Terbaik</div>
                        <div class="text-xs text-[#9BA4A6]">Pilih olahraga favoritmu</div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 p-4 flex items-center gap-3 hover:border-[#FF7A00] transition-colors">
                    <img src="{{ asset('assets/icons/dollar-circle.svg') }}" class="w-6 h-6" alt="Hadiah" />
                    <div class="text-left">
                        <div class="text-sm font-semibold text-[#18192B]">Hadiah Menarik</div>
                        <div class="text-xs text-[#9BA4A6]">Menang dan dapatkan reward</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="fixed bottom-0 left-0 right-0 max-w-[640px] mx-auto bg-white border-t border-gray-200 z-50">
            <div class="grid grid-cols-3 text-xs text-gray-500 py-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center justify-center p-1 hover:bg-gray-50 rounded-lg">
                    <img src="{{ asset('assets/icons/3dcube.svg') }}" class="w-6 h-6 mb-1" />
                    <span>Home</span>
                </a>
                <a href="{{ route('browse-events') }}" class="flex flex-col items-center justify-center p-1 hover:bg-gray-50 rounded-lg">
                    <img src="{{ asset('assets/icons/event.svg') }}" class="w-6 h-6 mb-1" />
                    <span>Events</span>
                </a>
                <a href="{{ route('browse-events') }}" class="flex flex-col items-center justify-center p-1 hover:bg-gray-50 rounded-lg">
                    <img src="{{ asset('assets/icons/search-nav.svg') }}" class="w-6 h-6 mb-1" />
                    <span>Search</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection