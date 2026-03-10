<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Venue;
use Carbon\Carbon;
use Tests\TestCase;

class MidtransCallbackTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        config(['midtrans.serverKey' => 'test-server-key']);
        
        // Create necessary models
        $category = EventCategory::create(['name' => 'Test Category', 'slug' => 'test-category']);
        $venue = Venue::create(['name' => 'Test Venue', 'address' => 'Test Address', 'postal_code' => '12345']);
        
        $this->event = Event::create([
            'category_id' => $category->id,
            'venue_id' => $venue->id,
            'title' => 'Test Event',
            'slug' => 'test-event',
            'description' => 'Test Description',
            'date' => Carbon::now()->addDays(30),
            'max_participants' => 100,
            'price' => 100000,
            'status' => 'open',
        ]);
    }

    private function generateSignature(string $orderId, string $statusCode, string $grossAmount): string
    {
        return hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.serverKey'));
    }

    /** @test */
    public function it_processes_settlement_callback()
    {
        $booking = Booking::create([
            'event_id' => $this->event->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'subtotal' => 100000,
            'tax' => 11000,
            'total' => 111000,
            'payment_status' => PaymentStatus::PENDING,
        ]);

        $signature = $this->generateSignature($booking->code, '200', '111000');

        $response = $this->postJson('/api/midtrans-callback', [
            'order_id' => $booking->code,
            'status_code' => '200',
            'gross_amount' => '111000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(200);
        
        $this->assertEquals(PaymentStatus::SUCCESS, $booking->fresh()->payment_status);
    }

    /** @test */
    public function it_processes_pending_callback()
    {
        $booking = Booking::create([
            'event_id' => $this->event->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'subtotal' => 100000,
            'tax' => 11000,
            'total' => 111000,
            'payment_status' => PaymentStatus::PENDING,
        ]);

        $signature = $this->generateSignature($booking->code, '201', '111000');

        $response = $this->postJson('/api/midtrans-callback', [
            'order_id' => $booking->code,
            'status_code' => '201',
            'gross_amount' => '111000',
            'signature_key' => $signature,
            'transaction_status' => 'pending',
        ]);

        $response->assertStatus(200);
        
        $this->assertEquals(PaymentStatus::PENDING, $booking->fresh()->payment_status);
    }

    /** @test */
    public function it_processes_expire_callback()
    {
        $booking = Booking::create([
            'event_id' => $this->event->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'subtotal' => 100000,
            'tax' => 11000,
            'total' => 111000,
            'payment_status' => PaymentStatus::PENDING,
        ]);

        $signature = $this->generateSignature($booking->code, '400', '111000');

        $response = $this->postJson('/api/midtrans-callback', [
            'order_id' => $booking->code,
            'status_code' => '400',
            'gross_amount' => '111000',
            'signature_key' => $signature,
            'transaction_status' => 'expire',
        ]);

        $response->assertStatus(200);
        
        $this->assertEquals(PaymentStatus::EXPIRED, $booking->fresh()->payment_status);
    }

    /** @test */
    public function it_rejects_invalid_signature()
    {
        $booking = Booking::create([
            'event_id' => $this->event->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'subtotal' => 100000,
            'tax' => 11000,
            'total' => 111000,
        ]);

        $response = $this->postJson('/api/midtrans-callback', [
            'order_id' => $booking->code,
            'status_code' => '200',
            'gross_amount' => '111000',
            'signature_key' => 'invalid-signature',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
        $this->assertEquals(PaymentStatus::PENDING, $booking->fresh()->payment_status);
    }

    /** @test */
    public function it_returns_404_for_unknown_transaction()
    {
        $signature = $this->generateSignature('UNKNOWN-ORDER', '200', '111000');

        $response = $this->postJson('/api/midtrans-callback', [
            'order_id' => 'UNKNOWN-ORDER',
            'status_code' => '200',
            'gross_amount' => '111000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_does_not_update_if_status_unchanged()
    {
        $booking = Booking::create([
            'event_id' => $this->event->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'subtotal' => 100000,
            'tax' => 11000,
            'total' => 111000,
            'payment_status' => PaymentStatus::SUCCESS,
        ]);

        $signature = $this->generateSignature($booking->code, '200', '111000');

        $response = $this->postJson('/api/midtrans-callback', [
            'order_id' => $booking->code,
            'status_code' => '200',
            'gross_amount' => '111000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(200);
        
        // Should still be SUCCESS (no change)
        $this->assertEquals(PaymentStatus::SUCCESS, $booking->fresh()->payment_status);
    }
}
