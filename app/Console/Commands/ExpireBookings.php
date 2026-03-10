<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire unpaid bookings that have passed their expiry time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting expired bookings cleanup...');

        $expiredBookings = Booking::where('payment_status', PaymentStatus::PENDING)
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No expired bookings found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredBookings->count()} expired booking(s) to process.");

        $updated = 0;
        foreach ($expiredBookings as $booking) {
            $booking->update([
                'payment_status' => PaymentStatus::EXPIRED,
            ]);

            Log::channel('daily')->info('Booking auto-expired', [
                'booking_id' => $booking->id,
                'code' => $booking->code,
                'event_id' => $booking->event_id,
                'expires_at' => $booking->expires_at->toIso8601String(),
            ]);

            $updated++;
        }

        $this->info("Successfully expired {$updated} booking(s).");

        return Command::SUCCESS;
    }
}
