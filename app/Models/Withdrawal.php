<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'commission',
        'net_amount',
        'bank_name',
        'account_number',
        'account_holder_name',
        'status',
        'notes',
        'proof_of_transfer',
        'requested_at',
        'approved_at',
        'rejected_at',
        'completed_at',
        'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    // Calculate commission (2%)
    public static function calculateCommission(float $amount): float
    {
        return round($amount * 0.02, 2);
    }

    // Calculate net amount
    public static function calculateNetAmount(float $amount): float
    {
        $commission = self::calculateCommission($amount);
        return round($amount - $commission, 2);
    }
}
