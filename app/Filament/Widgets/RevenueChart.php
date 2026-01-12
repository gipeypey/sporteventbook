<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue (Last 6 Months)';

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $user = auth()->user();

        $months = collect(range(5, 0))->map(function ($month) use ($user) {
            $date = Carbon::now()->subMonths($month);

            $query = Booking::where('payment_status', 'success')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            if ($user && $user->isVenueOwner()) {
                $query->whereHas('event.venue', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }

            return [
                'month' => $date->format('M Y'),
                'revenue' => $query->sum('total'),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
                    'data' => $months->pluck('revenue')->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                ],
            ],
            'labels' => $months->pluck('month')->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'elements' => [
                'bar' => [
                    'borderWidth' => 0,
                    'borderColor' => 'transparent',
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false, // hapus garis vertikal grid
                    ],
                    'ticks' => [
                        'display' => true, // tetap tampilkan bulan
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false, // hapus garis horizontal grid
                    ],
                    'ticks' => [
                        'display' => true, // tetap tampilkan angka
                    ],
                ],
            ],
            'spacing' => 0,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
