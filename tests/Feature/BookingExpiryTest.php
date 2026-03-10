<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Venue;
use Carbon\Carbon;
use Tests\TestCase;

class BookingExpiryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create necessary models for booking
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

    /** @test */
    public function it_sets_expires_at_on_booking_creation()
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

        $this->assertNotNull($booking->expires_at);
        $this->assertTrue($booking->expires_at->diffInMinutes(now()) < 31);
    }

    /** @test */
    public function it_checks_if_booking_is_expired()
    {
        $booking = Booking::create([
            'event_id' => $this->event->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'subtotal' => 100000,
            'tax' => 11000,
            'total' => 111000,
            'expires_at' => Carbon::now()->subMinutes(5),
        ]);

        $this->assertTrue($booking->isExpired());
    }

    /** @test */
    public function it_checks_if_booking_is_not_expired()
    {
        $booking = Booking::create([
            'event_id' => $this->event->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'subtotal' => 100000,
            'tax' => 11000,
            'total' => 111000,
            'expires_at' => Carbon::now()->addMinutes(25),
        ]);

        $this->assertFalse($booking->isExpired());
    }

    /** @test */
    public function expire_command_marks_pending_bookings_as_expired()
    {
        $expiredBooking = Booking::create([
            'event_id' => $this->event->id,
            'name' => 'Expired User',
            'email' => 'expired@example.com',
            'phone' => '08123456789',
            'subtotal' => 100000,
            'tax' => 11000,
            'total' => 111000,
            'payment_status' => PaymentStatus::PENDING,
            'expires_at' => Carbon::now()->subMinutes(5),
        ]);

        $activeBooking = Booking::create([
            'event_id' => $this->event->id,
            'name' => 'Active User',
            'email' => 'active@example.com',
            'phone' => '08123456789',
            'subtotal' => 100000,
            'tax' => 11000,
            'total' => 111000,
            'payment_status' => PaymentStatus::PENDING,
            'expires_at' => Carbon::now()->addMinutes(25),
        ]);

        $this->artisan('bookings:expire')
            ->expectsOutputToContain('Successfully expired 1 booking(s)')
            ->assertExitCode(0);

        $this->assertEquals(PaymentStatus::EXPIRED, $expiredBooking->fresh()->payment_status);
        $this->assertEquals(PaymentStatus::PENDING, $activeBooking->fresh()->payment_status);
    }

    /** @test */
    public function expire_command_skips_non_pending_bookings()
    {
        $successBooking = Booking::create([
            'event_id' => $this->event->id,
            'name' => 'Success User',
            'email' => 'success@example.com',
            'phone' => '08123456789',
            'subtotal' => 100000,
            'tax' => 11000,
            'total' => 111000,
            'payment_status' => PaymentStatus::SUCCESS,
            'expires_at' => Carbon::now()->subMinutes(5),
        ]);

        $this->artisan('bookings:expire')
            ->assertExitCode(0);

        $this->assertEquals(PaymentStatus::SUCCESS, $successBooking->fresh()->payment_status);
    }
}
