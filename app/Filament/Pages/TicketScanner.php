<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class TicketScanner extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::QrCode;

    protected static UnitEnum|string|null $navigationGroup = 'Operasi';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.ticket-scanner';

    protected static ?string $navigationLabel = 'Ticket Scanner';

    protected static ?string $title = 'Ticket Scanner';

    public function validateTicket(string $code, ?string $timezone = null): array
    {
        try {
            // Set default timezone to server timezone if not provided
            $timezone = $timezone ?? config('app.timezone');

            $booking = Booking::where('code', $code)
                ->with(['event.venue'])
                ->first();

            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'Ticket tidak ditemukan!',
                ];
            }

            // Check if venue owner is scanning ticket for their own venue
            $user = auth()->user();
            if ($user && $user->isVenueOwner()) {
                $venueId = $booking->event->venue->id ?? null;
                $userVenueIds = $user->venues()->pluck('id')->toArray();

                if (!in_array($venueId, $userVenueIds)) {
                    return [
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk check-in ticket event ini!',
                    ];
                }
            }

            if ($booking->payment_status !== 'success') {
                return [
                    'success' => false,
                    'message' => 'Ticket belum dibayar!',
                    'booking' => $this->formatBookingData($booking),
                ];
            }

            if ($booking->is_checked_in) {
                return [
                    'success' => false,
                    'message' => 'Ticket sudah pernah di-scan sebelumnya!',
                    'booking' => $this->formatBookingData($booking),
                    'checked_in_at' => $booking->checked_in_at?->timezone($timezone)->format('d F Y, H:i'),
                ];
            }

            // Update booking status with browser timezone
            $booking->update([
                'is_checked_in' => true,
                'checked_in_at' => now($timezone),
            ]);

            return [
                'success' => true,
                'message' => 'Check-in berhasil!',
                'booking' => $this->formatBookingData($booking),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }

    private function formatBookingData(Booking $booking): array
    {
        return [
            'code' => $booking->code,
            'name' => $booking->name,
            'email' => $booking->email,
            'phone' => $booking->phone,
            'event_name' => $booking->event->title ?? 'N/A',
            'event_date' => $booking->event->date ?? 'N/A',
            'venue_name' => $booking->event->venue->name ?? 'N/A',
            'payment_status' => ucfirst($booking->payment_status),
        ];
    }
}
