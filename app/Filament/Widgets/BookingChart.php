<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class BookingChart extends ChartWidget
{
    protected ?string $heading = 'Booking Trends';

    protected static ?int $sort = 1;

    protected ?string $maxHeight = '400px';

    protected int $daysCount = 7;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'elements' => [
                'line' => [
                    'tension' => 0.4,
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
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => true,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => true,
                        'color' => '#f0f0f0',
                    ],
                    'ticks' => [
                        'display' => true,
                        'stepSize' => 1,
                    ],
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    #[On('updateBookingDays')]
    public function updateDays(int $days): void
    {
        $this->daysCount = $days;
        $this->heading = "Booking Trends (Last {$days} " . ($days === 1 ? 'Day' : 'Days') . ")";
        $this->dispatch('updateChart');
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $days = $this->daysCount;

        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');

            $query = Booking::whereDate('created_at', $date);

            if ($user && $user->isVenueOwner()) {
                $query->whereHas('event.venue', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }

            $data[] = $query->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => $data,
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.1)',
                    'tension' => 0.4,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
