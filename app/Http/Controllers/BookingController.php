<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\BookingRepositoryInterface;
use App\Http\Requests\BookingInformationRequest;
use App\Http\Requests\CheckBookingRequest;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class BookingController extends Controller
{
    private EventRepositoryInterface $eventRepository;
    private BookingRepositoryInterface $bookingRepository;

    public function __construct(
        EventRepositoryInterface $eventRepository,
        BookingRepositoryInterface $bookingRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->bookingRepository = $bookingRepository;
    }

    public function show(string $slug, Request $request)
    {
        $event = $this->eventRepository->getBySlug($slug);

        return view('bookings.step-one', [
            'event' => $event,
            'status' => $request->query('status', 'event'),
        ]);
    }

    public function information(string $slug)
    {
        $event = $this->eventRepository->getBySlug($slug);

        return view('bookings.information', compact('event'));
    }

    public function saveInformation(BookingInformationRequest $request, string $slug)
    {
        $event = $this->eventRepository->getBySlug($slug);

        $request->merge([
            'event_id' => $event->id,
        ]);

        $this->bookingRepository->saveInformation($request->all());

        return redirect()->route('bookings.checkout', $slug);
    }

    public function checkout(string $slug)
    {
        $event = $this->eventRepository->getBySlug($slug);

        // Validate session before showing checkout
        $validation = $this->bookingRepository->validateSession();

        if (!$validation['valid']) {
            return redirect()->route('bookings.information', $slug)
                ->with('error', $validation['error']);
        }

        $bookingData = $validation['data'];

        $subTotal = $event->price;
        $tax = $subTotal * (config('pricing.tax_rate', 11) / 100);
        $insurance = config('pricing.insurance_fee', 0);
        
        // Get promo code from session
        $promoData = $this->bookingRepository->getSessionPromoCode();
        $discount = $promoData['discount'] ?? 0;
        $grandTotal = max(0, $subTotal + $tax + $insurance - $discount);
        
        // Calculate expiry time
        $expiresAt = now()->addMinutes((int) config('pricing.booking_session_expiry', 30));

        return view('bookings.step-two', [
            'event' => $event,
            'subTotal' => $subTotal,
            'tax' => $tax,
            'insurance' => $insurance,
            'grandTotal' => $grandTotal,
            'discount' => $discount,
            'promoData' => $promoData,
            'bookingData' => $bookingData,
            'expiresAt' => $expiresAt,
        ]);
    }

    /**
     * Apply promo code via AJAX
     */
    public function applyPromoCode(string $slug, Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string|max:50',
        ]);

        $result = $this->bookingRepository->savePromoCode($request->promo_code);

        return response()->json($result);
    }

    /**
     * Remove promo code
     */
    public function removePromoCode(string $slug)
    {
        $this->bookingRepository->clearPromoCode();

        return response()->json([
            'success' => true,
            'message' => 'Promo code removed',
        ]);
    }

    public function payment(string $slug)
    {
        $event = $this->eventRepository->getBySlug($slug);

        try {
            $paymentUrl = $this->bookingRepository->createBooking($event);
            
            // Send booking confirmation email
            $booking = Booking::where('code', session('last_booking_code'))->first();
            if ($booking) {
                $booking->sendConfirmationEmail();
            }
            
            return redirect($paymentUrl);
        } catch (Exception $e) {
            return redirect()->route('bookings.checkout', $slug)
                ->with('error', $e->getMessage());
        }
    }

    public function finished(Request $request)
    {
        $booking = $this->bookingRepository->getByCode($request->order_id);

        if (!$booking) {
            return redirect()->route('home')
                ->with('error', 'Booking not found');
        }

        // Check payment status from Midtrans directly if still pending
        if ($booking->payment_status?->value === 'pending') {
            try {
                // Set Midtrans config
                \Midtrans\Config::$serverKey = config('midtrans.serverKey');
                \Midtrans\Config::$isProduction = config('midtrans.isProduction');
                \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
                \Midtrans\Config::$is3ds = config('midtrans.is3ds');

                // Get transaction status from Midtrans
                $midtransStatus = \Midtrans\Transaction::status($booking->code);
                
                if ($midtransStatus) {
                    $transactionStatus = $midtransStatus->transaction_status;
                    $fraudStatus = $midtransStatus->fraud_status ?? null;
                    
                    $newStatus = match ($transactionStatus) {
                        'settlement', 'capture' => $fraudStatus === 'accept' ? PaymentStatus::SUCCESS : PaymentStatus::PENDING,
                        'pending' => PaymentStatus::PENDING,
                        'deny', 'expire', 'cancel' => PaymentStatus::FAILED,
                        default => PaymentStatus::UNKNOWN,
                    };

                    if ($booking->payment_status !== $newStatus) {
                        $booking->update(['payment_status' => $newStatus]);
                        
                        // Send ticket email if payment is successful
                        if ($newStatus === PaymentStatus::SUCCESS) {
                            $booking->sendTicketEmail();
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to check Midtrans transaction status', [
                    'booking_code' => $booking->code,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('bookings.finished', compact('booking'));
    }

    public function check()
    {
        return view('bookings.check');
    }

    public function checkBooking(CheckBookingRequest $request)
    {
        $booking = $this->bookingRepository->getByCodeAndEmail($request->booking_id, $request->email);

        if (!$booking) {
            return redirect()->route('bookings.check')->with('error', 'Booking not found');
        }

        return redirect()->route('bookings.ticket', $booking->code);
    }

    public function ticket(string $slug)
    {
        $booking = $this->bookingRepository->getByCode($slug);

        if (!$booking) {
            return redirect()->route('bookings.check')
                ->with('error', 'Ticket not found');
        }
        
        // Check and update expired status
        $this->bookingRepository->checkAndUpdateExpiredBooking($booking);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice(string $slug)
    {
        $booking = $this->bookingRepository->getByCode($slug);

        if (!$booking) {
            return redirect()->route('bookings.check')
                ->with('error', 'Booking not found');
        }

        return $booking->downloadInvoice();
    }
}
