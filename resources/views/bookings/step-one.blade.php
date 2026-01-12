@extends('layouts.main')

@section('content')
    <div class="bg-[#F6F8FA] min-h-screen">
        <div class="relative flex flex-col w-full max-w-[640px] min-h-screen mx-auto bg-[#F6F8FA] overflow-x-hidden">
            <!-- HEADER SECTION -->
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
                                    <div class="w-12 h-12 bg-[#F46325] rounded-full flex items-center justify-center">
                                        <img src="{{ asset('assets/icons/event-2.svg') }}"
                                            class="w-6 h-6 filter brightness-0 invert" alt="booking" />
                                    </div>
                                    <div
                                        class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-5 h-5 bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] rounded-full flex items-center justify-center">
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
                                        class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-5 h-5 bg-white rounded-full flex items-center justify-center">
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

            <!-- CONTENT SECTION -->
            <div class="px-5 -mt-[130px] relative z-10 space-y-5 pb-12">
                <!-- Event Registration Form -->
                <div class="bg-white rounded-3xl p-3 flex items-center gap-4">
                    <div class="relative w-[120px] h-[140px] flex-shrink-0">
                        <div class="w-full h-full rounded-3xl overflow-hidden">
                            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}"
                                class="w-full h-full object-cover" />
                        </div>
                    </div>

                    <!-- Event Info -->
                    <div class="flex-1 space-y-3">
                        <h3 class="font-bold text-base text-[#06071C] leading-tight">{{ $event->title }}</h3>

                        <div class="space-y-2">
                            <!-- Price -->
                            <div class="flex items-center gap-1.5">
                                <img src="{{ asset('assets/icons/dollar-circle.svg') }}" class="w-5 h-5 filter brightness-0"
                                    alt="price" />
                                <span class="text-[#06071C] text-sm">Rp
                                    {{ number_format($event->price, 0, ',', '.') }}</span>
                            </div>

                            <!-- Category -->
                            <div class="flex items-center gap-1.5">
                                <img src="{{ asset('assets/icons/crown.svg') }}" class="w-5 h-5 filter brightness-0"
                                    alt="category" />
                                <span class="text-[#06071C] text-sm">{{ $event->category->name ?? 'General' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information Form -->
                <form method="POST" action="{{ route('bookings.information.save', $event->slug) }}"
                    class="bg-white rounded-3xl p-5 pb-8 space-y-4">
                    @csrf

                    <!-- Header -->
                    <div class="flex items-center justify-between">
                        <h2 class="font-bold text-base text-[#06071C]">Personal Information</h2>
                        <img src="{{ asset('assets/icons/arrow-circle-down.svg') }}" class="w-6 h-6 transition-transform"
                            alt="expand" />
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-dashed border-[#E4E5E9]"></div>

                    <!-- Complete Name Field -->
                    <div class="space-y-2 py-3">
                        <label class="block font-semibold text-base text-[#06071C]">Complete Name</label>
                        <div
                            class="bg-[#F6F8FA] rounded-full px-4 py-3.5 flex items-center gap-2.5 border transition-colors hover:border-[#FF7A00] @error('name') border-red-500 @enderror">
                            <img src="{{ asset('assets/icons/user-octagon.svg') }}" class="w-6 h-6" alt="user" />
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="What's your name"
                                class="bg-transparent flex-1 text-base text-[#06071C] placeholder-gray-400 outline-none"
                                autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required />
                        </div>

                        @error('name')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number Field -->
                    <div class="space-y-2 py-3">
                        <label class="block font-semibold text-base text-[#06071C]">Phone Number</label>
                        <div
                            class="bg-[#F6F8FA] rounded-full px-4 py-3.5 flex items-center gap-2.5 border transition-colors hover:border-[#FF7A00] @error('phone') border-red-500 @enderror">
                            <img src="{{ asset('assets/icons/call-incoming.svg') }}" class="w-6 h-6" alt="phone" />
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                placeholder="What's your phone number"
                                class="bg-transparent flex-1 text-base text-[#06071C] placeholder-gray-400 outline-none"
                                autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required />

                            @error('phone')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email Address Field -->
                    <div class="space-y-2 py-3">
                        <label class="block font-semibold text-base text-[#06071C]">Email Address</label>
                        <div
                            class="bg-[#F6F8FA] rounded-full px-4 py-3.5 flex items-center gap-2.5 border transition-colors hover:border-[#FF7A00] @error('email') border-red-500 @enderror">
                            <img src="{{ asset('assets/icons/sms.svg') }}" class="w-6 h-6" alt="email" />
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="What's your email address"
                                class="bg-transparent flex-1 text-base text-[#06071C] placeholder-gray-400 outline-none"
                                autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />
                        </div>

                        @error('email')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Continue Button -->
                    <div class="pt-6">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] rounded-full py-3.5 px-5 flex items-center justify-between">
                            <span class="font-bold text-base text-[#06071C]">Continue</span>
                            <img src="{{ asset('assets/icons/arrow-left.svg') }}" class="w-5 h-5 transform rotate-180"
                                alt="continue" />
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
