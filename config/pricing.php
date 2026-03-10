<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Booking Tax Rate
    |--------------------------------------------------------------------------
    |
    | The tax rate applied to all bookings (in percentage).
    | Default: 11% (Indonesia PPN rate)
    |
    */
    'tax_rate' => env('BOOKING_TAX_RATE', 11),

    /*
    |--------------------------------------------------------------------------
    | Insurance Fee
    |--------------------------------------------------------------------------
    |
    | Fixed insurance fee for bookings (in Rupiah).
    | Set to 0 to disable insurance fee.
    |
    */
    'insurance_fee' => env('BOOKING_INSURANCE_FEE', 0),

    /*
    |--------------------------------------------------------------------------
    | Withdrawal Commission Rate
    |--------------------------------------------------------------------------
    |
    | The commission rate charged to venue owners for withdrawals (in percentage).
    | Default: 2%
    |
    */
    'withdrawal_commission_rate' => env('WITHDRAWAL_COMMISSION_RATE', 2),

    /*
    |--------------------------------------------------------------------------
    | Booking Session Expiry
    |--------------------------------------------------------------------------
    |
    | How long (in minutes) a booking session remains valid before expiring.
    | Default: 30 minutes
    |
    */
    'booking_session_expiry' => env('BOOKING_SESSION_EXPIRY', 30),
];
