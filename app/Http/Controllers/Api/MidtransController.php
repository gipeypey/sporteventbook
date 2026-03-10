<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;
        $signatureKey = $request->signature_key;
        $transactionStatus = $request->transaction_status;
        $paymentType = $request->payment_type;
        $fraudStatus = $request->fraud_status;

        // Log incoming callback for debugging
        Log::channel('daily')->info('Midtrans callback received', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => $transactionStatus,
            'payment_type' => $paymentType,
            'fraud_status' => $fraudStatus,
            'signature_key' => $signatureKey,
        ]);

        // Validate signature key with proper type casting
        $serverKey = config('midtrans.serverKey');
        
        // Cast gross_amount to string to ensure consistent format
        $grossAmountString = (string) $grossAmount;
        
        $hashedKey = hash('sha512', $orderId . $statusCode . $grossAmountString . $serverKey);

        if ($hashedKey !== $signatureKey) {
            Log::channel('daily')->warning('Midtrans callback - Invalid signature key', [
                'order_id' => $orderId,
                'expected' => $hashedKey,
                'received' => $signatureKey,
            ]);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        // Find booking by code
        $transaction = Booking::where('code', $orderId)->first();

        if (!$transaction) {
            Log::channel('daily')->error('Midtrans callback - Transaction not found', [
                'order_id' => $orderId,
            ]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $oldStatus = $transaction->payment_status;
        $newStatus = $this->determinePaymentStatus($transactionStatus, $paymentType, $fraudStatus);

        // Only update if status changed
        if ($oldStatus !== $newStatus) {
            $transaction->update(['payment_status' => $newStatus]);

            Log::channel('daily')->info('Midtrans callback - Payment status updated', [
                'order_id' => $orderId,
                'old_status' => $oldStatus?->value ?? 'null',
                'new_status' => $newStatus->value,
                'transaction_status' => $transactionStatus,
            ]);

            // Send ticket email if payment is successful
            if ($newStatus === PaymentStatus::SUCCESS && $oldStatus !== PaymentStatus::SUCCESS) {
                $transaction->sendTicketEmail();
            }
        } else {
            Log::channel('daily')->debug('Midtrans callback - Payment status unchanged', [
                'order_id' => $orderId,
                'status' => $newStatus->value,
            ]);
        }

        return response()->json([
            'message' => 'Callback processed successfully',
            'order_id' => $orderId,
            'status' => $newStatus->value,
        ], 200);
    }

    /**
     * Determine the payment status based on Midtrans transaction status
     */
    private function determinePaymentStatus(string $transactionStatus, ?string $paymentType, ?string $fraudStatus): \App\Enums\PaymentStatus
    {
        return match ($transactionStatus) {
            'capture' => $this->handleCaptureStatus($paymentType, $fraudStatus),
            'settlement' => \App\Enums\PaymentStatus::SUCCESS,
            'pending' => \App\Enums\PaymentStatus::PENDING,
            'deny' => \App\Enums\PaymentStatus::FAILED,
            'expire' => \App\Enums\PaymentStatus::EXPIRED,
            'cancel' => \App\Enums\PaymentStatus::CANCELED,
            default => \App\Enums\PaymentStatus::UNKNOWN,
        };
    }

    /**
     * Handle capture status for credit card transactions
     */
    private function handleCaptureStatus(?string $paymentType, ?string $fraudStatus): \App\Enums\PaymentStatus
    {
        if ($paymentType !== 'credit_card') {
            return \App\Enums\PaymentStatus::UNKNOWN;
        }

        return match ($fraudStatus) {
            'challenge' => \App\Enums\PaymentStatus::PENDING,
            'accept' => \App\Enums\PaymentStatus::SUCCESS,
            'reject' => \App\Enums\PaymentStatus::FAILED,
            default => \App\Enums\PaymentStatus::UNKNOWN,
        };
    }
}
