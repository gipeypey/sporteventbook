<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminder emails for pending bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting payment reminder emails...');

        // Get pending bookings that are not expired yet and created more than 1 hour ago
        $pendingBookings = Booking::where('payment_status', PaymentStatus::PENDING)
            ->where('expires_at', '>', now())
            ->where('created_at', '<', now()->subHour())
            ->whereDoesntHave('statusHistories', function ($query) {
                // Exclude bookings that already received a reminder
                $query->where('source', 'reminder')
                      ->where('created_at', '>', now()->subHours(6));
            })
            ->with(['event'])
            ->get();

        if ($pendingBookings->isEmpty()) {
            $this->info('No pending bookings need reminders.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pendingBookings->count()} booking(s) to send reminders.");

        $sent = 0;
        foreach ($pendingBookings as $booking) {
            try {
                $booking->sendPaymentReminderEmail();
                
                // Log the reminder
                \App\Models\BookingStatusHistory::log(
                    $booking,
                    PaymentStatus::PENDING->value,
                    PaymentStatus::PENDING->value,
                    'Payment reminder email sent',
                    'reminder',
                    [
                        'sent_at' => now()->toIso8601String(),
                    ]
                );

                $this->line("✓ Sent reminder for booking: {$booking->code} ({$booking->email})");
                $sent++;
            } catch (\Exception $e) {
                Log::error('Failed to send payment reminder', [
                    'booking_id' => $booking->id,
                    'code' => $booking->code,
                    'error' => $e->getMessage(),
                ]);
                $this->error("✗ Failed for booking: {$booking->code}");
            }
        }

        $this->info("Successfully sent {$sent}/{$pendingBookings->count()} reminder(s).");

        return Command::SUCCESS;
    }
}
