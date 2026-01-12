@extends('layouts.main')

@section('content')
    <div class="relative flex flex-col w-full max-w-[640px] min-h-screen mx-auto bg-white overflow-x-hidden">
        <!-- HERO IMAGE SECTION -->
        <div class="relative overflow-hidden">
            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}"
                class="w-full h-[28rem] object-cover object-[50%_70%] hero-image scale-95 md:scale-100 transition-transform" />

            <div class="absolute top-0 left-0 right-0 h-40 header-overlay z-40"></div>

            <!-- HEADER SECTION -->
            <div class="absolute header-section left-0 right-0 px-4 py-4 flex items-center justify-between z-50">
                <a href="{{ route('home') }}" class="text-white bg-white rounded-full p-2">
                    <img src="{{ asset('assets/icons/arrow-left.svg') }}" class="w-5 h-5" alt="back" />
                </a>
                <h1 class="text-white font-semibold text-lg header-text-shadow">Event Details</h1>
                <button class="text-white bg-white rounded-full p-2">
                    <img src="{{ asset('assets/icons/save-add.svg') }}" class="w-5 h-5" alt="save" />
                </button>
            </div>
        </div>

        <!-- CONTENT SECTION - Card putih yang overlap dengan image -->
        <div class="relative bg-white px-6 pt-12 pb-8 -mt-12 z-10" style="border-radius: 50px 50px 0 0;">
            <!-- Badge -->
            <div class="flex justify-center mb-6 -mt-6">
                @if ($event->status === 'open')
                    <span class="bg-[#552BFF] text-white px-4 py-2 rounded-2xl text-sm font-semibold">STILL OPEN</span>
                @elseif ($event->status === 'closed')
                    <span class="bg-red-500 text-white px-4 py-2 rounded-2xl text-sm font-semibold">CLOSED</span>
                @elseif ($event->status === 'ended')
                    <span class="bg-[#06071C] text-white px-4 py-2 rounded-full text-sm font-semibold">EVENT ENDED</span>
                @endif
            </div>

            <!-- Title -->
            <h2 class="text-xl font-semibold text-gray-800 mb-8 leading-tight text-center">
                {{ $event->title }}
            </h2>

            @if ($event->status === 'ended')
                <!-- Winner Section -->
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] rounded-2xl p-4 flex items-center gap-3">
                        <img src="{{ asset('assets/icons/crown.svg') }}" class="w-6 h-6 filter brightness-0"
                            alt="crown" />
                        <div class="flex flex-col">
                            <span class="text-black font-medium text-base">1st Event Winner</span>
                            <span class="text-black font-semibold text-lg">
                                @if ($event->winner_name)
                                    {{ $event->winner_name }}@if ($event->winner_number)
                                        • No {{ $event->winner_number }}
                                    @endif
                                @else
                                    Event Winner • No 001
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Key Information Cards -->
            <div class="bg-[#F6F8FA] py-6 mb-8 -mx-6 px-6">
                <div class="grid grid-cols-2 gap-4">
                    <div
                        class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm h-32 flex flex-col justify-between transition-colors group hover:bg-[#F46325] hover:border-[#F46325]">
                        <div class="flex items-center mb-2">
                            <img src="{{ asset('assets/icons/dollar-circle-gray.svg') }}"
                                class="w-8 h-8 transition group-hover:filter group-hover:brightness-0 group-hover:invert"
                                alt="prize" />
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <p
                                class="text-base font-bold text-gray-800 mb-1 flex items-center transition-colors group-hover:text-white">
                                <span class="text-sm font-bold mr-1">Rp</span>
                                <span>{{ number_format($event->total_prize, 0, ',', '.') }}</span>
                            </p>
                            <p class="text-sm text-gray-500 transition-colors group-hover:text-white/80">Total Prize</p>
                        </div>
                    </div>

                    <div
                        class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm h-32 flex flex-col justify-between transition-colors group hover:bg-[#F46325] hover:border-[#F46325]">
                        <div class="flex items-center mb-2">
                            <img src="{{ asset('assets/icons/calendar.svg') }}"
                                class="w-8 h-8 filter brightness-0 transition group-hover:invert" alt="date" />
                        </div>
                        <div class="flex-1 flex flex-col justify-center min-w-0">
                            <p
                                class="text-base font-bold text-gray-800 mb-1 transition-colors group-hover:text-white overflow-hidden whitespace-nowrap text-ellipsis">
                                {{ $event->date->locale('en')->translatedFormat('d M Y') }}
                            </p>
                            <p class="text-sm text-gray-500 transition-colors group-hover:text-white/80">Event Started</p>
                        </div>
                    </div>

                    <div
                        class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm h-32 flex flex-col justify-between transition-colors group hover:bg-[#F46325] hover:border-[#F46325]">
                        <div class="flex items-center mb-2">
                            <img src="{{ asset('assets/icons/people.svg') }}"
                                class="w-8 h-8 transition group-hover:filter group-hover:brightness-0 group-hover:invert"
                                alt="participants" />
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <p class="text-base font-bold text-gray-800 mb-1 transition-colors group-hover:text-white">
                                {{ $event->max_participants }} People
                            </p>
                            <p class="text-sm text-gray-500 transition-colors group-hover:text-white/80">Total Participants
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm h-32 flex flex-col justify-between transition-colors group hover:bg-[#F46325] hover:border-[#F46325]">
                        <div class="flex items-center mb-2">
                            <img src="{{ asset('assets/icons/crown-grey.svg') }}"
                                class="w-8 h-8 transition group-hover:filter group-hover:brightness-0 group-hover:invert"
                                alt="type" />
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <p class="text-base font-bold text-gray-800 mb-1 transition-colors group-hover:text-white">
                                {{ $event->category->name }}
                            </p>
                            <p class="text-sm text-gray-500 transition-colors group-hover:text-white/80">Event Type</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event With Us Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Event With Us</h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ $event->description }}
                </p>
            </div>

            @if ($event->prizes->count() > 0)
                <!-- Additional Prizes Section -->
                <div class="bg-[#F6F8FA] py-6 mb-6 -mx-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 px-6">Additional Prizes</h3>
                    <div class="overflow-x-auto hide-scrollbar">
                        <div class="flex gap-4 w-max px-6">
                            @foreach ($event->prizes as $prize)
                                <div
                                    class="bg-white border border-gray-200 rounded-xl w-[148px] h-[165px] flex-shrink-0 shadow flex flex-col items-start pt-[10px] px-[12px] pb-[4px] transition-colors hover:border-[#FF7A00]">
                                    @if ($prize->image)
                                        <img src="{{ asset('storage/' . $prize->image) }}" alt="{{ $prize->name }}"
                                            class="w-[120px] h-[90px] object-cover rounded-[12px] mb-2" />
                                    @else
                                        <div
                                            class="w-[120px] h-[90px] bg-gray-200 rounded-[12px] mb-2 flex items-center justify-center">
                                            <img src="{{ asset('assets/icons/gift.svg') }}" class="w-8 h-8 text-gray-400"
                                                alt="prize" />
                                        </div>
                                    @endif
                                    <div class="w-[112px] h-[41px] text-left flex flex-col justify-center leading-tight">
                                        <p class="text-[14px] font-bold text-gray-900">{{ $prize->name }}</p>
                                        <p class="text-[12px] text-gray-400 mt-[2px]">
                                            {{ $prize->given_by ?? 'by Event Organizer' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Important Details -->
            <div class="mt-8 mb-24 w-full border border-gray-300 rounded-xl bg-white shadow p-6 text-sm divide-y">
                <h2 class="font-semibold text-lg text-black mb-4">Important Details</h2>

                <!-- Row 1 -->
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/icons/note.svg') }}" alt="participants icon" class="w-5 h-5">
                        <span class="text-gray-600">Participants</span>
                    </div>
                    <span class="font-semibold text-gray-900">
                        {{ $event->bookings_count ?? 0 }} of {{ $event->max_participants }} People
                    </span>
                </div>

                <!-- Row 2 -->
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/icons/note.svg') }}" alt="venue icon" class="w-5 h-5">
                        <span class="text-gray-600">Venue</span>
                    </div>
                    <span class="font-semibold text-gray-900">{{ $event->venue->name }}</span>
                </div>

                <!-- Row 3 -->
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/icons/note.svg') }}" alt="post code icon" class="w-5 h-5">
                        <span class="text-gray-600">Post Code</span>
                    </div>
                    <span class="font-semibold text-gray-900">{{ $event->venue->postal_code ?? 'N/A' }}</span>
                </div>

                <!-- Row 4 -->
                <div class="flex items-center justify-between py-4 border-b-0">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/icons/note.svg') }}" alt="status icon" class="w-5 h-5">
                        <span class="text-gray-600">Status</span>
                    </div>
                    @if ($event->status === 'open')
                        <span class="font-semibold text-gray-900">Open Registration</span>
                    @elseif ($event->status === 'closed')
                        <span class="font-semibold text-red-500">Registration Closed</span>
                    @elseif ($event->status === 'ended')
                        <span class="font-semibold text-gray-900">Event Ended</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Join Event Bottom Bar -->
        <div
            class="absolute bottom-0 left-0 right-0 max-w-[640px] mx-auto bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between z-50">
            <div class="flex-1 min-w-0">
                <p class="text-lg font-bold text-gray-900 leading-tight">Rp
                    {{ number_format($event->price, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500">Fee One Person</p>
            </div>

            @if ($event->status === 'open')
                <a href="{{ route('bookings.show', $event->slug) }}"
                    class="flex-none bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] text-gray-800 font-semibold px-6 py-3 rounded-full shadow-md text-base ml-4 whitespace-nowrap inline-flex items-center justify-center">
                    Join Event
                </a>
            @else
                <div
                    class="flex-none bg-[#E4E5E9] text-[#B6B7BE] font-semibold px-6 py-3 rounded-full shadow-md text-base ml-4 select-none whitespace-nowrap inline-flex items-center justify-center">
                    Event Closed
                </div>
            @endif

        </div>
    </div>
@endsection
