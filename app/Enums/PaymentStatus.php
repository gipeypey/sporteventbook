<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case EXPIRED = 'expired';
    case CANCELED = 'canceled';
    case UNKNOWN = 'unknown';

    /**
     * Get the display label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
            self::EXPIRED => 'Expired',
            self::CANCELED => 'Canceled',
            self::UNKNOWN => 'Unknown',
        };
    }

    /**
     * Get the color for the status (for UI badges)
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
            self::EXPIRED => 'gray',
            self::CANCELED => 'gray',
            self::UNKNOWN => 'gray',
        };
    }

    /**
     * Check if the status is a successful payment
     */
    public function isSuccess(): bool
    {
        return $this === self::SUCCESS;
    }

    /**
     * Check if the status is pending
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if the status is a failed payment
     */
    public function isFailed(): bool
    {
        return in_array($this, [self::FAILED, self::EXPIRED, self::CANCELED]);
    }

    /**
     * Get all statuses as an array for select dropdowns
     */
    public static function options(): array
    {
        return [
            self::PENDING->value => self::PENDING->label(),
            self::SUCCESS->value => self::SUCCESS->label(),
            self::FAILED->value => self::FAILED->label(),
            self::EXPIRED->value => self::EXPIRED->label(),
            self::CANCELED->value => self::CANCELED->label(),
        ];
    }
}
