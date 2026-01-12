<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BookingChart extends ChartWidget
{
    protected ?string $heading = 'Booking Trends (Last 7 Days)';

    protected static ?int $sort = 1;

    protected ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $user = auth()->user();

        $days = collect(range(6, 0))->map(function ($day) use ($user) {
            $date = Carbon::now()->subDays($day);

            $query = Booking::whereDate('created_at', $date);

            if ($user && $user->isVenueOwner()) {
                $query->whereHas('event.venue', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }

            return [
                'date' => $date->format('M d'),
                'bookings' => $query->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => $days->pluck('bookings')->toArray(),
                    'borderColor' => '#f97316',
                    'backgroundColor' => '#f97316',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $days->pluck('date')->toArray(),
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
