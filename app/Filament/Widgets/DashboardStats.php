<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use App\Models\Venue;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user && $user->isVenueOwner()) {
            return [
                $this->getBalanceStat(),
                $this->getVenueStat(),
                $this->getEventStat(),
                $this->getBookingStat(),
                $this->getPendingBookingStat(),
            ];
        }

        return [
            $this->getUserStat(),
            $this->getEventStat(),
            $this->getBookingStat(),
            $this->getVenueStat(),
            $this->getCategoryStat(),
            $this->getPendingBookingStat(),
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

        return Stat::make('Total Bookings', Booking::count())
            ->description('Event bookings')
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
            $count = Booking::where('payment_status', 'pending')
                ->whereHas('event.venue', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count();

            return Stat::make('Pending Bookings', $count)
                ->description('Awaiting confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger');
        }

        return Stat::make('Pending Bookings', Booking::where('payment_status', 'pending')->count())
            ->description('Awaiting confirmation')
            ->descriptionIcon('heroicon-m-clock')
            ->color('danger');
    }
}
