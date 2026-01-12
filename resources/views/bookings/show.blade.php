@extends('layouts.main')

@section('content')
    <div class="bg-[#F6F8FA] min-h-screen p-5">
        <div class="relative flex flex-col w-full max-w-[640px] min-h-screen mx-auto bg-[#F6F8FA] overflow-x-hidden">
            <div class="space-y-5">
                <!-- Main Ticket Card -->
                <div class="bg-white rounded-3xl p-5 relative overflow-hidden">
                    <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-[#F6F8FA] rounded-full"></div>
                    <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-[#F6F8FA] rounded-full"></div>

                    <div class="space-y-4">
                        <h1 class="text-[#06071C] text-base font-bold text-center">Entrance Ticket</h1>
                        <!-- Divider -->
                        <div class="border-t border-dashed border-[#E4E5E9]"></div>
                        <div class="w-full h-[120px] rounded-2xl overflow-hidden relative">
                            <img src="{{ asset('storage/' . $booking->event->image) }}" class="w-full h-full object-contain"
                                alt="event" />
                        </div>

                        <div class="space-y-2.5">
                            <div class="flex gap-8">
                                <div class="flex-1 flex items-center gap-2.5">
                                    <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6 filter brightness-0"
                                        alt="note" />
                                    <div class="flex-1">
                                        <p class="text-[#9BA4A6] text-sm font-normal">Code</p>
                                        <p class="text-[#06071C] text-base font-bold">
                                            {{ $booking->code }}
                                        </p>
                                    </div>
                                </div>
                                <!-- Participant Card -->
                                <div class="flex-1 flex items-center gap-2.5">
                                    <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6 filter brightness-0"
                                        alt="note" />
                                    <div class="flex-1">
                                        <p class="text-[#9BA4A6] text-sm font-normal">Participant</p>
                                        <p class="text-[#06071C] text-base font-bold">{{ $booking->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: payment_status Cards -->
                            <div class="flex gap-8">
                                <!-- Status Not Started -->
                                <div class="flex-1 flex items-center gap-2.5">
                                    <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6 filter brightness-0"
                                        alt="note" />
                                    <div class="flex-1">
                                        <p class="text-[#9BA4A6] text-sm font-normal">Status</p>
                                        <p class="text-[#06071C] text-base font-bold">Not Started</p>
                                    </div>
                                </div>
                                <!-- Booking Status -->
                                <div class="flex-1 flex items-center gap-2.5">
                                    <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6 filter brightness-0"
                                        alt="note" />
                                    <div class="flex-1">
                                        <p class="text-[#9BA4A6] text-sm font-normal">Booking</p>
                                        <p class="text-[#06071C] text-base font-bold">
                                            {{ ucfirst($booking->payment_status) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3: Venue (Full Width) -->
                            <div class="flex items-center gap-2.5">
                                <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6 filter brightness-0"
                                    alt="note" />
                                <div class="flex-1">
                                    <p class="text-[#9BA4A6] text-sm font-normal">Venue</p>
                                    <p class="text-[#06071C] text-base font-bold">{{ $booking->event->venue->name }}</p>
                                </div>
                            </div>

                            <!-- Row 4: Post Code and Started At -->
                            <div class="flex gap-8">
                                <!-- Post Code Card -->
                                <div class="flex-1 flex items-center gap-2.5">
                                    <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6 filter brightness-0"
                                        alt="note" />
                                    <div>
                                        <p class="text-[#9BA4A6] text-sm font-normal">Post Code</p>
                                        <p class="text-[#06071C] text-base font-bold">
                                            {{ $booking->event->venue->postal_code ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <!-- Started At Card -->
                                <div class="flex-1 flex items-center gap-2.5">

                                    <img src="{{ asset('assets/icons/note.svg') }}" class="w-6 h-6 filter brightness-0"
                                        alt="note" />
                                    <div class="flex-1">
                                        <p class="text-[#9BA4A6] text-sm font-normal">Started At</p>
                                        <p class="text-[#06071C] text-base font-bold">
                                            {{ \Carbon\Carbon::parse($booking->event->date)->format('d F Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-dashed border-[#E4E5E9]"></div>
                    </div>
                </div>

                <!-- Bottom Card -->
                <div class="bg-white rounded-3xl p-5">
                    <!-- Header with Dropdown -->
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-[#06071C] text-base font-bold">Entrance Ticket</h2>
                        <img src="{{ asset('assets/icons/arrow-circle-down.svg') }}" class="w-6 h-6 filter brightness-0"
                            alt="dropdown" />
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-dashed border-[#E4E5E9] mb-4"></div>

                    @if ($booking->payment_status === 'pending')
                        <!-- Large Icon -->
                        <div class="flex justify-center mb-4">
                            <div class="w-[50px] h-[50px] bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>

                        <p>
                            Pembayaran masih pending sehingga tiket anda belum bisa kami berikan
                        </p>
                    @else
                        <!-- QR Code Section -->
                        <div class="space-y-3">
                            <div class="text-center">
                                <h3 class="text-[#06071C] text-sm font-semibold mb-2">Scan QR Code untuk Check-in</h3>
                            </div>

                            <div class="flex flex-col items-center">
                                {!! QrCode::size(200)->generate($booking->code) !!}
                                <p
                                    class="text-[#9BA4A6] text-xs font-mono bg-gray-50 px-3 py-1 rounded-full inline-block text-center mt-2">
                                    {{ $booking->code }}
                                </p>
                            </div>

                            <!-- Booking Code Display -->
                            <div class="text-center">
                                <p class="text-[#9BA4A6] text-xs font-mono bg-gray-50 px-3 py-1 rounded-full inline-block">
                                    {{ $booking->event->code }}
                                </p>
                            </div>

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
