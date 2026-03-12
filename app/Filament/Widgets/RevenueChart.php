<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue (Last 6 Months)';

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '400px';

    protected int $monthsCount = 6;

    protected ?int $daysCount = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'elements' => [
                'bar' => [
                    'borderWidth' => 0,
                    'borderColor' => 'transparent',
                    'borderRadius' => 4,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return "Rp " + Number(context.raw).toLocaleString(); }',
                    ],
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
                        'callback' => 'function(value) { return "Rp " + (value/1000000).toFixed(1) + "M"; }',
                    ],
                ],
            ],
        ];
    }

    #[On('updateRevenueMonths')]
    public function updateMonths(int $months): void
    {
        $this->monthsCount = $months;
        $this->daysCount = null;
        $this->heading = "Revenue (Last {$months} " . ($months === 1 ? 'Month' : 'Months') . ")";
        $this->dispatch('updateChart');
    }

    #[On('updateRevenueDays')]
    public function updateDays(int $days): void
    {
        $this->daysCount = $days;
        $this->heading = "Revenue (Last {$days} " . ($days === 1 ? 'Day' : 'Days') . ")";
        $this->dispatch('updateChart');
    }

    protected function getData(): array
    {
        $user = auth()->user();

        // Use days-based filtering if set
        if ($this->daysCount !== null) {
            return $this->getDaysData($user);
        }

        // Otherwise use months-based filtering
        return $this->getMonthsData($user);
    }

    protected function getMonthsData($user): array
    {
        $months = $this->monthsCount;

        $monthsData = collect(range($months - 1, 0))->map(function ($month) use ($user) {
            $date = Carbon::now()->subMonths($month);

            $query = Booking::with(['event.venue'])
                ->where('payment_status', PaymentStatus::SUCCESS)
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
                    'data' => $monthsData->pluck('revenue')->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => $monthsData->pluck('month')->toArray(),
        ];
    }

    protected function getDaysData($user): array
    {
        $days = $this->daysCount;

        $daysData = collect(range($days - 1, 0))->map(function ($day) use ($user) {
            $date = Carbon::now()->subDays($day);

            $query = Booking::with(['event.venue'])
                ->where('payment_status', PaymentStatus::SUCCESS)
                ->whereDate('created_at', $date);

            if ($user && $user->isVenueOwner()) {
                $query->whereHas('event.venue', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }

            return [
                'day' => $date->format('M d'),
                'revenue' => $query->sum('total'),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
                    'data' => $daysData->pluck('revenue')->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => $daysData->pluck('day')->toArray(),
        ];
    }
}
