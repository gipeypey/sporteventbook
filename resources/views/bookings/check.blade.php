@extends('layouts.main')

@section('content')
    <div class="bg-[#552BFF] min-h-screen">
        <div class="relative flex flex-col w-full max-w-[640px] min-h-screen mx-auto bg-[#552BFF] overflow-x-hidden">
            <!-- PROFILE CARDS SECTION -->
            <div class="relative pt-0 pb-6 px-4 -mt-8">
                <div class="flex justify-center">
                    <div class="w-96 h-96">
                        <img src="{{ asset('assets/images/group.png') }}" alt="Group Profile"
                            class="w-full h-full object-contain" />
                    </div>
                </div>

                <!-- BOOKING FORM SECTION -->
                <div class="bg-white rounded-3xl px-6 pt-6 pb-1 -mt-16 mb-32">
                    <div class="mb-6 pb-1">
                        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Check Your Booking</h1>
                        <div class="border-b border-dashed border-gray-300 mb-6"></div>

                        <form method="POST" action="{{ route('bookings.check.booking') }}">
                            @csrf
                            <!-- Booking ID Input -->
                            <div class="mb-4">
                                <label class="block text-base font-semibold text-black mb-2">Booking ID</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <img src="{{ asset('assets/icons/note-favorite.svg') }}"
                                            class="w-5 h-5 text-gray-400" alt="calendar" />
                                    </div>
                                    <input type="text" name="booking_id" value="{{ old('booking_id') }}"
                                        placeholder="What's your booking ID"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-full bg-gray-50 text-gray-900 text-sm font-normal placeholder-gray-400 transition-colors hover:border-[#FF7A00] focus:border-[#FF7A00] outline-none @error('booking_id') border-red-500 @enderror">
                                </div>

                                @error('booking_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Address Input -->
                            <div class="mb-6">
                                <label class="block text-base font-semibold text-black mb-2">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <img src="{{ asset('assets/icons/sms.svg') }}" class="w-5 h-5 text-gray-400"
                                            alt="email" />
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        placeholder="What's your email address"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-full bg-gray-50 text-gray-900 text-sm font-normal placeholder-gray-400 transition-colors hover:border-[#FF7A00] focus:border-[#FF7A00] outline-none"
                                        autocomplete="off" autocorrect="off" autocapitalize="off"
                                        spellcheck="false @error('email') border-red-500 @enderror">
                                </div>


                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dotted line after email input -->
                            <div class="border-b border-dashed border-gray-300 mb-6"></div>

                            @if (session('error'))
                                <div class="bg-[#F46325] rounded-3xl p-4 mb-6">
                                    <div class="flex items-center">
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ asset('assets/icons/event.svg') }}"
                                                class="w-5 h-5 text-white icon-white" alt="warning" />
                                        </div>
                                        <div class="ml-3 text-white text-sm font-semibold">
                                            <p>Booking not found, silahkan coba gunakan informasi lain</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Submit Button -->
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] rounded-full py-3.5 px-5 flex items-center justify-center">
                                <span class="font-bold text-base text-[#06071C]">Find My Booking</span>
                            </button>
                        </form>
                    </div>
                </div>
                <!-- BOTTOM NAVIGATION BAR -->
                <div class="fixed bottom-0 left-0 right-0 max-w-[640px] mx-auto bg-white border-t border-gray-200 z-50">
                    <div class="grid grid-cols-5 text-xs text-gray-500 py-2">

                        <!-- Browse -->
                        <a href="{{ route('home') }}"
                            class="flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 rounded-lg p-1 transition-colors">
                            <img src="{{ asset('assets/icons/3dcube-2.svg') }}" class="w-6 h-6 mb-1" />
                            <span class="text-gray-500">Browse</span>
                        </a>

                        <!-- My Event -->
                        <a href="{{ route('bookings.check') }}"
                            class="flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 rounded-lg p-1 transition-colors">
                            <img src="{{ asset('assets/icons/event-2.svg') }}" class="w-6 h-6 mb-1 event-icon-active" />
                            <span class="text-black font-semibold">My Event</span>
                        </a>

                        <!-- Spacer (tengah) -->
                        <div></div>

                        <!-- Rewards -->
                        <a href="#"
                            class="flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 rounded-lg p-1 transition-colors">
                            <img src="{{ asset('assets/icons/gift.svg') }}" class="w-6 h-6 mb-1" />
                            <span>Rewards</span>
                        </a>

                        <!-- Helps -->
                        <a href="#"
                            class="flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 rounded-lg p-1 transition-colors">
                            <img src="{{ asset('assets/icons/setting-2.svg') }}" class="w-6 h-6 mb-1" />
                            <span>Helps</span>
                        </a>
                    </div>

                    <!-- Floating Search Button -->
                    <div
                        class="absolute -top-6 left-1/2 -translate-x-1/2 cursor-pointer hover:scale-105 transition-transform">
                        <a href="{{ route('events.browse') }}">
                            <img src="{{ asset('assets/icons/search-nav.svg') }}" class="w-16 h-16" alt="Search" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
