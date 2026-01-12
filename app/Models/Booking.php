<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'code',
        'event_id',
        'name',
        'phone',
        'email',
        'payment_status',
        'is_checked_in',
        'checked_in_at',
        'subtotal',
        'tax',
        'insurance',
        'total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'insurance' => 'decimal:2',
        'total' => 'decimal:2',
        'is_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->code)) {
                $booking->code = 'BK' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
