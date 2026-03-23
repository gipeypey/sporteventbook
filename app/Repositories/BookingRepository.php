<?php

namespace App\Repositories;

use App\Enums\PaymentStatus;
use App\Interfaces\BookingRepositoryInterface;
use App\Models\Booking;
use App\Models\Event;
use App\Models\PromoCode;
use Exception;

class BookingRepository implements BookingRepositoryInterface
{
    private const SESSION_KEY = 'booking_data';

    private function getSessionExpiryMinutes(): int
    {
        return config('pricing.booking_session_expiry', 30);
    }

    public function getAllBookings(
        ?string $search,
        ?int $eventId,
        ?string $status,
        ?string $email,
        ?int $limit,
    ) {
        $query = Booking::with(['event.venue', 'event.category'])->where(function ($query) use ($search, $eventId, $status, $email) {
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', $search)
                        ->orWhere('name', 'LIKE', '%' . $search . '%');
                });
            }

            if ($eventId) {
                $query->where('event_id', $eventId);
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($email) {
                $query->where('email', $email);
            }
        });

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getByCode(string $code)
    {
        return Booking::with(['event.venue', 'event.category'])->where('code', $code)->first();
    }

    public function getByCodeAndEmail(string $code, string $email)
    {
        return Booking::with(['event.venue', 'event.category'])
            ->where('code', $code)
            ->where('email', $email)
            ->first();
    }

    /**
     * Check if a booking is expired and update its status
     */
    public function checkAndUpdateExpiredBooking(Booking $booking): void
    {
        if ($booking->payment_status === PaymentStatus::PENDING && $booking->isExpired()) {
            $booking->update([
                'payment_status' => PaymentStatus::EXPIRED,
            ]);
        }
    }

    public function saveInformation(array $data)
    {
        // Get event price to calculate subtotal
        $event = \App\Models\Event::find($data['event_id']);
        $subTotal = $event ? $event->price : 0;
        
        session([
            self::SESSION_KEY => [
                'event_id' => $data['event_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'subtotal' => $subTotal,
                'saved_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Save promo code to session
     */
    public function savePromoCode(?string $promoCode): array
    {
        if (!$promoCode) {
            session()->forget('promo_code');
            return ['success' => true, 'message' => 'Promo code removed'];
        }

        $promo = \App\Models\PromoCode::where('code', $promoCode)->first();

        if (!$promo) {
            return ['success' => false, 'error' => 'Promo code not found'];
        }

        $bookingData = session(self::SESSION_KEY);
        $subtotal = $bookingData['subtotal'] ?? 0;

        if (!$promo->isValid($subtotal)) {
            return ['success' => false, 'error' => 'Promo code is not valid for this order'];
        }

        $discount = $promo->calculateDiscount($subtotal);

        session(['promo_code' => [
            'code' => $promo->code,
            'name' => $promo->name,
            'type' => $promo->type,
            'value' => $promo->value,
            'discount' => $discount,
        ]]);

        return [
            'success' => true,
            'message' => 'Promo code applied successfully',
            'discount' => $discount,
        ];
    }

    /**
     * Get promo code from session
     */
    public function getSessionPromoCode(): ?array
    {
        return session('promo_code');
    }

    /**
     * Clear promo code from session
     */
    public function clearPromoCode(): void
    {
        session()->forget('promo_code');
    }

    /**
     * Validate that booking session exists and is not expired
     */
    public function validateSession(): array
    {
        $bookingData = session(self::SESSION_KEY);

        if (!$bookingData) {
            return [
                'valid' => false,
                'error' => 'Booking session not found. Please restart the booking process.',
                'error_code' => 'SESSION_NOT_FOUND',
            ];
        }

        // Check if session has expired
        if (isset($bookingData['saved_at'])) {
            $savedAt = \Carbon\Carbon::parse($bookingData['saved_at']);
            if ($savedAt->diffInMinutes(now()) > $this->getSessionExpiryMinutes()) {
                $this->clearSession();
                $this->clearPromoCode();
                return [
                    'valid' => false,
                    'error' => 'Booking session has expired. Please restart the booking process.',
                    'error_code' => 'SESSION_EXPIRED',
                ];
            }
        }

        // Validate required fields
        $requiredFields = ['event_id', 'name', 'email', 'phone', 'subtotal'];
        foreach ($requiredFields as $field) {
            if (empty($bookingData[$field])) {
                return [
                    'valid' => false,
                    'error' => "Booking information is incomplete. Missing: {$field}",
                    'error_code' => 'INCOMPLETE_DATA',
                ];
            }
        }

        return [
            'valid' => true,
            'data' => $bookingData,
        ];
    }

    /**
     * Get validated booking session data
     */
    public function getValidatedSessionData(): ?array
    {
        $validation = $this->validateSession();

        if (!$validation['valid']) {
            return null;
        }

        return $validation['data'];
    }

    /**
     * Clear booking session
     */
    public function clearSession(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * Refresh session expiry time
     */
    public function refreshSession(): void
    {
        $bookingData = session(self::SESSION_KEY);
        if ($bookingData) {
            $bookingData['saved_at'] = now()->toIso8601String();
            session([self::SESSION_KEY => $bookingData]);
        }
    }

    /**
     * Get subtotal from session
     */
    public function getSessionSubtotal(): float
    {
        $bookingData = session(self::SESSION_KEY);
        return $bookingData['subtotal'] ?? 0;
    }

    public function createBooking(Event $event)
    {
        // Validate session before creating booking
        $validation = $this->validateSession();

        if (!$validation['valid']) {
            throw new Exception($validation['error']);
        }

        $bookingData = $validation['data'];

        // Verify event is still available
        if (!$event || $event->status !== 'open') {
            $this->clearSession();
            throw new Exception('Event is no longer available for booking');
        }

        // Check if event is full
        $currentBookings = $event->bookings()->where('payment_status', 'success')->count();
        if ($currentBookings >= $event->max_participants) {
            $this->clearSession();
            throw new Exception('Event is already full');
        }

        $subTotal = $event->price;
        $tax = $subTotal * (config('pricing.tax_rate', 11) / 100);
        $insurance = config('pricing.insurance_fee', 0);
        
        // Apply promo code discount
        $promoData = $this->getSessionPromoCode();
        $discount = $promoData['discount'] ?? 0;
        $grandTotal = max(0, $subTotal + $tax + $insurance - $discount);

        $transaction = Booking::create([
            'event_id' => $bookingData['event_id'],
            'name' => $bookingData['name'],
            'email' => $bookingData['email'],
            'phone' => $bookingData['phone'],
            'subtotal' => $subTotal,
            'tax' => $tax,
            'insurance' => $insurance,
            'total' => $grandTotal,
            'promo_code' => $promoData['code'] ?? null,
            'discount' => $discount,
            'final_total' => $grandTotal,
            'expires_at' => now()->addMinutes((int) config('pricing.booking_session_expiry', 30)),
        ]);

        // Increment promo usage if used
        if ($promoData) {
            $promo = PromoCode::where('code', $promoData['code'])->first();
            if ($promo) {
                $promo->increment('used_count');
            }
        }

        // Store booking code in session for email sending
        session(['last_booking_code' => $transaction->code]);

        // Clear session after successful booking creation
        $this->clearSession();
        $this->clearPromoCode();

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->code,
                'gross_amount' => $transaction->total,
            ],
            'customer_details' => [
                'first_name' => $transaction->name,
                'email' => $transaction->email,
                'phone' => $transaction->phone,
            ],
        ];

        $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

        return $paymentUrl;
    }
}
