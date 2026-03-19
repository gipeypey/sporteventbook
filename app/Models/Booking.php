<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingTicketMail;
use App\Mail\PaymentReminderMail;
use App\Traits\GeneratesInvoicePdf;

class Booking extends Model
{
    use GeneratesInvoicePdf;

    protected $fillable = [
        'code',
        'event_id',
        'name',
        'phone',
        'email',
        'payment_status',
        'expires_at',
        'is_checked_in',
        'checked_in_at',
        'subtotal',
        'tax',
        'insurance',
        'total',
        'promo_code',
        'discount',
        'final_total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'insurance' => 'decimal:2',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_total' => 'decimal:2',
        'is_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
        'expires_at' => 'datetime',
        'payment_status' => PaymentStatus::class,
    ];

    protected static function booted(): void
    {
        // Log payment status changes
        static::updating(function (Booking $booking) {
            if ($booking->isDirty('payment_status')) {
                $oldStatus = $booking->getOriginal('payment_status');
                
                // Convert enum to string if needed
                if ($oldStatus instanceof PaymentStatus) {
                    $oldStatus = $oldStatus->value;
                }
                
                $newStatus = $booking->payment_status?->value ?? $booking->payment_status;

                BookingStatusHistory::log(
                    $booking,
                    $oldStatus,
                    $newStatus,
                    'Status changed via ' . (app('request')->route()?->uri() ?? 'unknown'),
                    'system',
                    [
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]
                );
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->code)) {
                $booking->code = 'BK' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
            
            // Set default payment status
            if (empty($booking->payment_status)) {
                $booking->payment_status = PaymentStatus::PENDING;
            }
            
            // Set expiry time (30 minutes from now, configurable)
            if (empty($booking->expires_at)) {
                $booking->expires_at = now()->addMinutes((int) config('pricing.booking_session_expiry', 30));
            }
        });
    }

    /**
     * Check if the booking has expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return now()->isAfter($this->expires_at);
    }

    /**
     * Check if the booking has a successful payment
     */
    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::SUCCESS;
    }

    /**
     * Check if the booking payment is pending
     */
    public function isPending(): bool
    {
        return $this->payment_status === PaymentStatus::PENDING;
    }

    /**
     * Check if the booking payment has failed
     */
    public function isFailed(): bool
    {
        return $this->payment_status && $this->payment_status->isFailed();
    }

    /**
     * Get the payment status label
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return $this->payment_status?->label() ?? 'Unknown';
    }

    /**
     * Get the payment status color
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return $this->payment_status?->color() ?? 'gray';
    }

    /**
     * Send booking confirmation email
     */
    public function sendConfirmationEmail(): void
    {
        try {
            Mail::to($this->email)->send(new BookingConfirmationMail($this));
        } catch (\Exception $e) {
            \Log::error('Failed to send booking confirmation email', [
                'booking_id' => $this->id,
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send ticket email after successful payment
     */
    public function sendTicketEmail(): void
    {
        try {
            Mail::to($this->email)->send(new BookingTicketMail($this));
        } catch (\Exception $e) {
            \Log::error('Failed to send ticket email', [
                'booking_id' => $this->id,
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send payment reminder email
     */
    public function sendPaymentReminderEmail(): void
    {
        try {
            Mail::to($this->email)->send(new PaymentReminderMail($this));
        } catch (\Exception $e) {
            \Log::error('Failed to send payment reminder email', [
                'booking_id' => $this->id,
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
