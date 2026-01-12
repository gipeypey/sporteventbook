<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;

class BookingStatusChart extends ChartWidget
{
    protected ?string $heading = 'Booking Status Distribution';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $user = auth()->user();

        $pendingQuery = Booking::where('payment_status', 'pending');
        $successQuery = Booking::where('payment_status', 'success');
        $cancelledQuery = Booking::where('payment_status', 'cancelled');
        $failedQuery = Booking::where('payment_status', 'failed');

        if ($user && $user->isVenueOwner()) {
            $pendingQuery->whereHas('event.venue', fn($q) => $q->where('user_id', $user->id));
            $successQuery->whereHas('event.venue', fn($q) => $q->where('user_id', $user->id));
            $cancelledQuery->whereHas('event.venue', fn($q) => $q->where('user_id', $user->id));
            $failedQuery->whereHas('event.venue', fn($q) => $q->where('user_id', $user->id));
        }

        $pending = $pendingQuery->count();
        $success = $successQuery->count();
        $cancelled = $cancelledQuery->count();
        $failed = $failedQuery->count();

        return [
            'datasets' => [
                [
                    'data' => [$pending, $success, $cancelled, $failed],
                    'backgroundColor' => ['#f59e0b', '#10b981', '#ef4444', '#6b7280'],
                ],
            ],
            'labels' => ['Pending', 'Success', 'Cancelled', 'Failed'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
