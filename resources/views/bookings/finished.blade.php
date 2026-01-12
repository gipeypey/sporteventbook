@extends('layouts.main')

@section('content')
    <div class="bg-[#F6F8FA] min-h-screen">
        <div class="relative flex flex-col w-full max-w-[640px] min-h-screen mx-auto bg-[#F6F8FA] overflow-x-hidden">
            <!-- Header Section -->
            <div class="relative">
                <div class="bg-[#552BFF] h-[390px] w-full"></div>
                <div class="absolute top-[60px] left-[20px] right-[20px] flex items-center justify-between">
                    <a href="{{ route('events.show', $booking->event->slug) }}"
                        class="w-12 h-12 bg-white rounded-full flex items-center justify-center cursor-pointer hover:bg-gray-50 transition-colors">
                        <img src="{{ asset('assets/icons/arrow-left.svg') }}" class="w-5 h-5" alt="back" />
                    </a>

                    <!-- Title -->
                    <h1 class="text-white font-semibold text-base">
                        Registration
                    </h1>
                    <div class="w-12 h-12 opacity-0">
                        <img src="{{ asset('assets/icons/save-add.svg') }}" class="w-5 h-5" alt="save" />
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="absolute top-[138px] left-[40px] right-[40px]">
                    <div class="relative">
                        <div class="absolute top-6 left-[24px] right-[24px]">
                            <div class="w-full border-t-2 border-dashed border-white opacity-80"></div>
                        </div>

                        <!-- Steps -->
                        <div class="flex justify-between relative z-10 gap-16">
                            <!-- Step 1 -->
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <div class="w-12 h-12  bg-[#06071C] rounded-full flex items-center justify-center">
                                        <img src="{{ asset('assets/icons/event-2.svg') }}"
                                            class="w-6 h-6 filter brightness-0 invert" alt="booking" />
                                    </div>
                                    <div
                                        class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-5 h-5 bg-white rounded-full flex items-center justify-center">
                                        <span class="text-[#06071C] text-xs font-bold">1</span>
                                    </div>
                                </div>
                                <span class="text-white text-xs font-medium mt-4">Booking</span>
                            </div>

                            <!-- Step 2 -->
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-[#06071C] rounded-full flex items-center justify-center">
                                        <img src="{{ asset('assets/icons/security-card.svg') }}"
                                            class="w-6 h-6 filter brightness-0 invert" alt="payment" />
                                    </div>
                                    <div
                                        class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-5 h-5  rounded-full flex items-center justify-center">
                                        <span class="text-[#06071C] text-xs font-bold">2</span>
                                    </div>
                                </div>
                                <span class="text-white text-xs font-medium mt-4">Payment</span>
                            </div>

                            <!-- Step 3 -->
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-[#F46325] rounded-full flex items-center justify-center">
                                        <img src="{{ asset('assets/icons/flag.svg') }}"
                                            class="w-6 h-6 filter brightness-0 invert" alt="get ready" />
                                    </div>
                                    <div
                                        class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-5 h-5 bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] rounded-full flex items-center justify-center">
                                        <span class="text-[#06071C] font-bold text-xs">3</span>
                                    </div>
                                </div>
                                <span class="text-white text-xs font-medium mt-4">Get Ready</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTENT SECTION -->
            <div class="px-5 -mt-[130px] relative z-10 space-y-5 pb-12">
                <!-- Booking Finished Content -->
                <div class="bg-white rounded-3xl p-5 space-y-5">
                    <!-- Success Title -->
                    <h2 class="text-[#06071C] text-xl font-semibold text-center">Booking Finished, Yay!</h2>

                    <!-- Divider -->
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>

                    <!-- Event Summary -->
                    <div class="flex items-start gap-4">
                        <div class="w-[120px] h-[140px] flex-shrink-0">
                            <img src="{{ asset('storage/' . $booking->event->image) }}"
                                class="w-full h-full object-cover rounded-3xl" alt="{{ $booking->event->title }}" />

                        </div>
                        <div class="flex-1 space-y-3">
                            <h3 class="text-[#06071C] text-base font-semibold leading-tight">{{ $booking->event->title }}
                            </h3>
                            <div class="space-y-2">
                                <div class="flex items-center gap-1.5">
                                    <img src="{{ asset('assets/icons/dollar-circle.svg') }}"
                                        class="w-5 h-5 filter brightness-0" alt="price" />
                                    <span class="text-[#06071C] text-sm">Rp
                                        {{ number_format($booking->event->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <img src="{{ asset('assets/icons/calendar.svg') }}" class="w-5 h-5 filter brightness-0"
                                        alt="date" />
                                    <span
                                        class="text-[#06071C] text-sm">{{ $booking->event->date->format('d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>

                    <!-- Description -->
                    <p class="text-[#06071C] text-base leading-7">
                        Gunakan informasi di bawah untuk memeriksa status pemesanan Anda
                    </p>

                    <!-- Booking ID -->
                    <div class="space-y-2">
                        <h4 class="text-[#06071C] text-base font-semibold">Booking ID</h4>
                        <div class="bg-[#F6F8FA] rounded-full px-4 py-3.5 flex items-center gap-2.5">
                            <img src="{{ asset('assets/icons/note-favorite.svg') }}" class="w-6 h-6 filter brightness-0"
                                alt="booking" />
                            <span class="text-[#06071C] text-base font-semibold">{{ $booking->code }}</span>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="space-y-2">
                        <h4 class="text-[#06071C] text-base font-semibold">Email Address</h4>
                        <div class="bg-[#F6F8FA] rounded-full px-4 py-3.5 flex items-center gap-2.5">
                            <img src="{{ asset('assets/icons/sms.svg') }}" class="w-6 h-6 filter brightness-0"
                                alt="email" />
                            <span class="text-[#06071C] text-base font-semibold">{{ $booking->email }}</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <!-- View Booking Details Button -->
                        <form method="POST" action="{{ route('bookings.check.booking') }}">
                            @csrf
                            <input type="hidden" name="booking_id" value="{{ $booking->code }}">
                            <input type="hidden" name="email" value="{{ $booking->email }}">
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] rounded-full py-3.5 px-5 flex items-center justify-between">
                                <span class="text-[#06071C] text-base font-bold">View My Booking Details</span>
                                <img src="{{ asset('assets/icons/arrow-left.svg') }}" class="w-5 h-5 rotate-180"
                                    alt="arrow" />
                            </button>
                        </form>

                        <!-- Explore Other Events Button -->
                        <a href="{{ route('events.browse') }}"
                            class="w-full bg-[#06071C] rounded-full py-3.5 px-5 flex items-center justify-center">
                            <span class="text-white text-base font-semibold">Explore Other Events</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
