<?php

namespace App\Filament\Pages;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Event;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use Illuminate\Support\Carbon;

class TicketScanner extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::QrCode;

    protected static UnitEnum|string|null $navigationGroup = 'Operasi';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.ticket-scanner';

    protected static ?string $navigationLabel = 'Ticket Scanner';

    protected static ?string $title = 'Ticket Scanner';

    public function getCheckInStats(): array
    {
        $user = auth()->user();
        
        $buildQuery = function ($query) use ($user) {
            if ($user && $user->isVenueOwner()) {
                $query->whereHas('event.venue', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
            return $query;
        };

        // Today's check-ins
        $todayCheckIns = $buildQuery(Booking::query())
            ->where('is_checked_in', true)
            ->whereDate('checked_in_at', Carbon::today())
            ->count();

        // Total check-ins
        $totalCheckIns = $buildQuery(Booking::query())
            ->where('is_checked_in', true)
            ->count();

        // Total successful bookings
        $totalSuccessful = $buildQuery(Booking::query())
            ->where('payment_status', PaymentStatus::SUCCESS)
            ->count();

        // Check-in rate
        $checkInRate = $totalSuccessful > 0 ? ($totalCheckIns / $totalSuccessful) * 100 : 0;

        // Upcoming events with check-in info
        $upcomingEvents = Event::where('date', '>=', Carbon::today())
            ->where('status', 'open')
            ->withCount([
                'bookings as total_bookings' => function ($q) {
                    $q->where('payment_status', PaymentStatus::SUCCESS);
                },
                'bookings as checked_in' => function ($q) {
                    $q->where('payment_status', PaymentStatus::SUCCESS)
                      ->where('is_checked_in', true);
                }
            ])
            ->orderBy('date', 'asc')
            ->limit(3)
            ->get();

        return [
            'today_check_ins' => $todayCheckIns,
            'total_check_ins' => $totalCheckIns,
            'check_in_rate' => $checkInRate,
            'upcoming_events' => $upcomingEvents->toArray(),
        ];
    }

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

            if ($booking->payment_status !== PaymentStatus::SUCCESS) {
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
            'payment_status' => $booking->payment_status?->label() ?? ucfirst($booking->payment_status),
        ];
    }
}
