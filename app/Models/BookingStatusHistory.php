<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingStatusHistory extends Model
{
    protected $fillable = [
        'booking_id',
        'old_status',
        'new_status',
        'reason',
        'source',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Log a status change
     */
    public static function log(
        Booking $booking,
        PaymentStatus|string|null $oldStatus,
        PaymentStatus|string $newStatus,
        ?string $reason = null,
        string $source = 'system',
        ?array $metadata = null
    ): self {
        // Convert enum to string if needed
        if ($oldStatus instanceof PaymentStatus) {
            $oldStatus = $oldStatus->value;
        }
        
        if ($newStatus instanceof PaymentStatus) {
            $newStatus = $newStatus->value;
        }
        
        return static::create([
            'booking_id' => $booking->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
            'source' => $source,
            'metadata' => $metadata ?? [],
        ]);
    }

    /**
     * Get the display label for the source
     */
    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'system' => 'System',
            'midtrans' => 'Midtrans',
            'admin' => 'Admin',
            'user' => 'User',
            default => ucfirst($this->source),
        };
    }
}
