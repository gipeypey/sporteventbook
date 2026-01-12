@extends('layouts.main')

@section('content')
    <div class="bg-[#F6F8FA] min-h-screen">
        <div class="relative flex flex-col w-full max-w-[640px] min-h-screen mx-auto bg-[#F6F8FA] overflow-x-hidden">

            <!-- Header Section -->
            <div class="relative">
                <div class="bg-[#552BFF] h-[390px] w-full"></div>
                <div class="absolute top-[60px] left-[20px] right-[20px] flex items-center justify-between">
                    <a href="{{ route('events.show', $event->slug) }}"
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
                                    <div class="w-12 h-12 bg-[#F46325] rounded-full flex items-center justify-center">
                                        <img src="{{ asset('assets/icons/security-card.svg') }}"
                                            class="w-6 h-6 filter brightness-0 invert" alt="payment" />
                                    </div>
                                    <div
                                        class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-5 h-5 bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] rounded-full flex items-center justify-center">
                                        <span class="text-[#06071C] text-xs font-bold">2</span>
                                    </div>
                                </div>
                                <span class="text-white text-xs font-medium mt-4">Payment</span>
                            </div>

                            <!-- Step 3 -->
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-[#06071C] rounded-full flex items-center justify-center">
                                        <img src="{{ asset('assets/icons/flag.svg') }}"
                                            class="w-6 h-6 filter brightness-0 invert" alt="get ready" />
                                    </div>
                                    <div
                                        class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-5 h-5 bg-white rounded-full flex items-center justify-center">
                                        <span class="text-[#06071C] font-bold text-xs">3</span>
                                    </div>
                                </div>
                                <span class="text-white text-xs font-medium mt-4">Get Ready</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Content Section -->
            <div class="px-4 space-y-4 pb-8">
                <div class="bg-white rounded-[32px] -mt-[100px] pt-4 px-6 py-6 shadow-sm relative z-20">
                    <h3 class="text-[#06071C] text-lg font-semibold mb-4 text-center">Event Summary</h3>

                    <!-- Event Summary Content -->
                    <div class="flex space-x-4">
                        <div class="w-[120px] h-[140px] flex-shrink-0">
                            @if ($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}"
                                    class="w-full h-full object-cover rounded-2xl" alt="{{ $event->title }}" />
                            @else
                                <img src="{{ asset('assets/images/women-tennis.png') }}"
                                    class="w-full h-full object-cover rounded-2xl" alt="{{ $event->title }}" />
                            @endif
                        </div>
                        <div class="flex-1 py-2">
                            <h3 class="text-[#06071C] text-base font-semibold mb-3 leading-normal">{{ $event->title }}</h3>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-1.5">
                                    <img src="{{ asset('assets/icons/dollar-circle-gray.svg') }}" class="w-4 h-4"
                                        alt="price" />
                                    <span class="text-[#06071C] text-sm">Rp
                                        {{ number_format($event->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center space-x-1.5">
                                    <img src="{{ asset('assets/icons/crown.svg') }}" class="w-4 h-4 filter brightness-0"
                                        alt="category" />
                                    <span class="text-[#06071C] text-sm">{{ $event->category->name ?? 'General' }}</span>
                                </div>
                                <div class="flex items-center space-x-1.5">
                                    <img src="{{ asset('assets/icons/calendar.svg') }}" class="w-4 h-4 filter brightness-0"
                                        alt="date" />
                                    <span class="text-[#06071C] text-sm">{{ $event->date->format('d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Booking Details -->
            <div class="bg-white rounded-2xl px-6 py-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-[#06071C] text-base font-bold">Booking Details</h2>
                    <img src="{{ asset('assets/icons/arrow-circle-down.svg') }}" class="w-6 h-6" alt="expand" />
                </div>
                <div class="space-y-3">
                    <!-- Sub Total -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6" alt="note" />
                            <span class="text-[#06071C] text-sm">Sub Total</span>
                        </div>
                        <span class="text-[#06071C] text-base font-bold">Rp
                            {{ number_format($subTotal, 0, ',', '.') }}</span>
                    </div>
                    <!-- Promo Code -->
                    <div class="flex items-center justify-between" id="promo-discount-row" style="display: none;">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('assets/icons/gift.svg') }}" class="w-6 h-6" alt="promo" />
                            <span class="text-[#06071C] text-sm">Discount (<span id="promo-code-name"></span>)</span>
                        </div>
                        <span class="text-green-600 text-base font-bold" id="promo-discount-amount">-Rp 0</span>
                    </div>
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>
                    <!-- Tax -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6" alt="tax" />
                            <span class="text-[#06071C] text-sm">Tax 11%</span>
                        </div>
                        <span class="text-[#06071C] text-base font-bold">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>
                    <!-- Insurance -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6" alt="insurance" />
                            <span class="text-[#06071C] text-sm">Insurance</span>
                        </div>
                        <span class="text-[#06071C] text-base font-bold">Rp
                            {{ number_format($insurance, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>
                    <!-- Merchandise -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6" alt="merchandise" />
                            <span class="text-[#06071C] text-sm">Merchandise</span>
                        </div>
                        <span class="text-[#06071C] text-base font-bold">Free (Included)</span>
                    </div>
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>
                    <!-- Grand Total -->
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6" alt="total" />
                            <span class="text-[#06071C] text-sm">Grand Total</span>
                        </div>
                        <span class="text-[#552BFF] text-[22px] font-bold" id="grand-total">Rp
                            {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Promo Section -->
                <div class="bg-[#06071C] rounded-2xl px-6 py-6 relative overflow-hidden mt-4">
                    <div class="mb-4">
                        <h3 class="text-white text-base font-bold">Get Big Discount</h3>
                    </div>
                    <div class="space-y-2 promo-content">
                        <p class="text-white text-base font-semibold">Promo Code</p>
                        <div class="bg-white rounded-full px-5 py-4 flex items-center space-x-3">
                            <img src="{{ asset('assets/icons/gift.svg') }}" class="w-6 h-6" alt="gift" />
                            <input type="text" id="promo-code" placeholder="Input your promo code"
                                class="flex-1 text-[#06071C] text-base bg-transparent outline-none placeholder:text-gray-400" />
                        </div>
                        <div id="promo-message" class="text-sm font-semibold hidden"></div>
                    </div>
                </div>

            </div>

            <!-- Payment Confirmation -->
            <div class="bg-white rounded-2xl px-6 py-6 shadow-sm space-y-6 mt-4">
                <div class="flex items-center justify-between cursor-pointer accordion-header">
                    <h2 class="text-[#06071C] text-base font-bold">Payment Information</h2>
                    <img src="{{ asset('assets/icons/arrow-circle-down.svg') }}"
                        class="w-6 h-6 transition-transform duration-300" alt="expand" />
                </div>

                <!-- isi accordion dibungkus -->
                <div class="accordion-content space-y-6 mt-4">
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>
                    <div class="space-y-2">
                        <div class="bg-blue-50 rounded-lg px-5 py-4 border border-blue-200">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-blue-800 text-sm font-medium">Secure Payment with Midtrans</p>
                                    <p class="text-blue-600 text-xs mt-1">You will be redirected to Midtrans secure payment
                                        page where you can choose your preferred payment method.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="confirm-payment"
                            class="w-5 h-5 rounded-2xl border-2 border-[#F46325] text-[#F46325] focus:ring-[#F46325] focus:ring-offset-2 accent-[#F46325]" />
                        <label for="confirm-payment" class="text-[#06071C] text-base font-semibold cursor-pointer">I agree
                            to proceed with payment</label>
                    </div>
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>
                    <form method="POST" action="{{ route('bookings.payment', $event->slug) }}" class="w-full"
                        enctype="multipart/form-data">
                        @csrf
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] text-[#06071C] font-bold text-base py-4 px-6 rounded-full flex items-center justify-between">
                            <span>Proceed to Payment</span>
                            <img src="{{ asset('assets/icons/arrow-left.svg') }}" class="w-5 h-5 rotate-180"
                                alt="arrow" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
