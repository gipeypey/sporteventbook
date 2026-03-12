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
                                            {{ $booking->payment_status?->label() ?? 'Unknown' }}
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

                    @if ($booking->payment_status?->value === 'pending')
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
                        <div class="space-y-4">
                            <div class="text-center">
                                <h3 class="text-[#06071C] text-sm font-semibold mb-2">Scan QR Code untuk Check-in</h3>
                                <p class="text-[#9BA4A6] text-xs">Tunjukkan QR code ini di meja check-in</p>
                            </div>

                            <!-- QR Code with Border -->
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-white border-2 border-[#552BFF] rounded-xl shadow-lg">
                                    {!! QrCode::size(220)->generate($booking->code) !!}
                                </div>
                                <p class="text-[#06071C] text-sm font-bold font-mono bg-gradient-to-r from-[#552BFF] to-[#7B5FE8] text-white px-4 py-2 rounded-full inline-block text-center mt-3 shadow-md">
                                    {{ $booking->code }}
                                </p>
                            </div>

                            <!-- Download & Share Buttons -->
                            <div class="flex gap-3 mt-4">
                                <button onclick="downloadTicket()" 
                                    class="flex-1 bg-[#552BFF] hover:bg-[#4325CC] text-white font-semibold py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download Ticket
                                </button>
                                <button onclick="shareTicket()" 
                                    class="flex-1 bg-[#F6F8FA] hover:bg-gray-100 text-[#06071C] font-semibold py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-all border border-gray-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                    Share
                                </button>
                            </div>

                            <!-- Check-in Status -->
                            @if($booking->is_checked_in)
                                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mt-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-green-800 font-semibold text-sm">Already Checked In</p>
                                            <p class="text-green-600 text-xs">{{ $booking->checked_in_at?->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-blue-800 font-semibold text-sm">Not Checked In Yet</p>
                                            <p class="text-blue-600 text-xs">Please arrive 30 minutes before event</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadTicket() {
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generating...';
            btn.disabled = true;

            // Capture the ticket card
            const ticketCard = document.querySelector('.bg-white.rounded-3xl');
            
            html2canvas(ticketCard, {
                scale: 2,
                backgroundColor: '#F6F8FA',
                logging: false,
                useCORS: true
            }).then(canvas => {
                // Convert to image and download
                const link = document.createElement('a');
                link.download = 'ticket-{{ $booking->code }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(err => {
                console.error('Error generating ticket:', err);
                // Fallback: use print
                window.print();
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }

        function shareTicket() {
            const bookingCode = '{{ $booking->code }}';
            const eventName = '{{ $booking->event->title }}';
            const eventDate = '{{ $booking->event->date->format("d F Y") }}';
            
            const shareData = {
                title: 'My Event Ticket',
                text: `I'm registered for ${eventName} on ${eventDate}! Booking code: ${bookingCode}`,
                url: window.location.href
            };

            if (navigator.share) {
                navigator.share(shareData)
                    .then(() => console.log('Shared successfully'))
                    .catch(console.error);
            } else {
                // Fallback: copy to clipboard
                const textToCopy = `${shareData.text}\n\nView ticket: ${shareData.url}`;
                navigator.clipboard.writeText(textToCopy)
                    .then(() => {
                        alert('Link copied to clipboard! You can now paste it anywhere.');
                    })
                    .catch(err => {
                        console.error('Failed to copy:', err);
                        // Last fallback: select and copy
                        prompt('Copy this link:', textToCopy);
                    });
            }
        }
    </script>
    @endpush
@endsection
