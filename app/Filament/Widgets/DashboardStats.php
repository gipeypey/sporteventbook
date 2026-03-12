<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use App\Models\Venue;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user && $user->isVenueOwner()) {
            return [
                $this->getBalanceStat(),
                $this->getTodayRevenueStat(),
                $this->getVenueStat(),
                $this->getEventStat(),
                $this->getBookingStat(),
                $this->getPendingBookingStat(),
                $this->getSuccessRateStat(),
            ];
        }

        return [
            $this->getUserStat(),
            $this->getTodayRevenueStat(),
            $this->getMonthRevenueStat(),
            $this->getEventStat(),
            $this->getBookingStat(),
            $this->getVenueStat(),
            $this->getPendingBookingStat(),
            $this->getSuccessRateStat(),
        ];
    }

    private function getBalanceStat(): Stat
    {
        $user = auth()->user();
        $balance = $user ? $user->getAvailableBalance() : 0;

        return Stat::make('Saldo Tersedia', 'Rp ' . number_format($balance, 0, ',', '.'))
            ->description('Saldo yang dapat ditarik')
            ->descriptionIcon('heroicon-m-banknotes')
            ->color('success')
            ->url(route('filament.admin.resources.withdrawals.create'))
            ->extraAttributes([
                'class' => 'cursor-pointer hover:opacity-80',
            ]);
    }

    private function getTodayRevenueStat(): Stat
    {
        $user = auth()->user();
        
        $query = Booking::whereDate('created_at', Carbon::today())
            ->where('payment_status', PaymentStatus::SUCCESS);
        
        if ($user && $user->isVenueOwner()) {
            $query->whereHas('event.venue', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        $todayRevenue = $query->sum('total');
        
        // Compare with yesterday
        $yesterdayQuery = Booking::whereDate('created_at', Carbon::yesterday())
            ->where('payment_status', PaymentStatus::SUCCESS);
        
        if ($user && $user->isVenueOwner()) {
            $yesterdayQuery->whereHas('event.venue', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        $yesterdayRevenue = $yesterdayQuery->sum('total');
        $growth = $yesterdayRevenue > 0 ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 0;

        $description = $growth >= 0 
            ? '↑ ' . number_format(abs($growth), 1) . '% vs yesterday' 
            : '↓ ' . number_format(abs($growth), 1) . '% vs yesterday';

        return Stat::make('Today Revenue', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
            ->description($description)
            ->descriptionIcon($growth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($growth >= 0 ? 'success' : 'danger');
    }

    private function getMonthRevenueStat(): Stat
    {
        $user = auth()->user();
        
        $query = Booking::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('payment_status', PaymentStatus::SUCCESS);
        
        if ($user && $user->isVenueOwner()) {
            $query->whereHas('event.venue', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        $monthRevenue = $query->sum('total');

        return Stat::make('This Month', 'Rp ' . number_format($monthRevenue, 0, ',', '.'))
            ->description('Monthly revenue')
            ->descriptionIcon('heroicon-m-calendar')
            ->color('info');
    }

    private function getUserStat(): Stat
    {
        return Stat::make('Total Users', User::count())
            ->description('Registered users')
            ->descriptionIcon('heroicon-m-users')
            ->color('success');
    }

    private function getEventStat(): Stat
    {
        $user = auth()->user();

        if ($user && $user->isVenueOwner()) {
            $count = Event::whereHas('venue', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();

            return Stat::make('My Events', $count)
                ->description('Events in my venues')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info');
        }

        return Stat::make('Total Events', Event::count())
            ->description('Active events')
            ->descriptionIcon('heroicon-m-calendar')
            ->color('info');
    }

    private function getBookingStat(): Stat
    {
        $user = auth()->user();

        if ($user && $user->isVenueOwner()) {
            $count = Booking::whereHas('event.venue', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();

            return Stat::make('My Bookings', $count)
                ->description('Bookings in my venues')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning');
        }

        // Add today bookings count
        $todayBookings = Booking::whereDate('created_at', Carbon::today())->count();
        $totalBookings = Booking::count();

        return Stat::make('Total Bookings', $totalBookings)
            ->description('Event bookings (' . $todayBookings . ' today)')
            ->descriptionIcon('heroicon-m-ticket')
            ->color('warning');
    }

    private function getVenueStat(): Stat
    {
        $user = auth()->user();

        if ($user && $user->isVenueOwner()) {
            $count = Venue::where('user_id', $user->id)->count();

            return Stat::make('My Venues', $count)
                ->description('Venues I manage')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary');
        }

        return Stat::make('Total Venues', Venue::count())
            ->description('Available venues')
            ->descriptionIcon('heroicon-m-building-office')
            ->color('primary');
    }

    private function getCategoryStat(): Stat
    {
        return Stat::make('Event Categories', EventCategory::count())
            ->description('Sport categories')
            ->descriptionIcon('heroicon-m-rectangle-stack')
            ->color('success');
    }

    private function getPendingBookingStat(): Stat
    {
        $user = auth()->user();

        if ($user && $user->isVenueOwner()) {
            $count = Booking::with(['event.venue'])
                ->where('payment_status', PaymentStatus::PENDING)
                ->whereHas('event.venue', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count();

            return Stat::make('Pending Bookings', $count)
                ->description('Awaiting confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger');
        }

        return Stat::make('Pending Bookings', Booking::where('payment_status', PaymentStatus::PENDING)->count())
            ->description('Awaiting confirmation')
            ->descriptionIcon('heroicon-m-clock')
            ->color('danger');
    }

    private function getSuccessRateStat(): Stat
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
        
        $totalBookings = $buildQuery(Booking::query())->count();
        $successfulBookings = $buildQuery(Booking::where('payment_status', PaymentStatus::SUCCESS))->count();
        
        $successRate = $totalBookings > 0 ? ($successfulBookings / $totalBookings) * 100 : 0;

        return Stat::make('Success Rate', number_format($successRate, 1) . '%')
            ->description($successfulBookings . ' of ' . $totalBookings . ' bookings')
            ->descriptionIcon('heroicon-m-check-circle')
            ->color($successRate >= 80 ? 'success' : ($successRate >= 60 ? 'warning' : 'danger'));
    }
}
