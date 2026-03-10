<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use Tests\TestCase;

class PaymentStatusEnumTest extends TestCase
{
    /** @test */
    public function it_has_correct_payment_status_values()
    {
        $this->assertEquals('pending', PaymentStatus::PENDING->value);
        $this->assertEquals('success', PaymentStatus::SUCCESS->value);
        $this->assertEquals('failed', PaymentStatus::FAILED->value);
        $this->assertEquals('expired', PaymentStatus::EXPIRED->value);
        $this->assertEquals('canceled', PaymentStatus::CANCELED->value);
        $this->assertEquals('unknown', PaymentStatus::UNKNOWN->value);
    }

    /** @test */
    public function it_returns_correct_labels()
    {
        $this->assertEquals('Pending', PaymentStatus::PENDING->label());
        $this->assertEquals('Success', PaymentStatus::SUCCESS->label());
        $this->assertEquals('Failed', PaymentStatus::FAILED->label());
        $this->assertEquals('Expired', PaymentStatus::EXPIRED->label());
        $this->assertEquals('Canceled', PaymentStatus::CANCELED->label());
    }

    /** @test */
    public function it_returns_correct_colors()
    {
        $this->assertEquals('warning', PaymentStatus::PENDING->color());
        $this->assertEquals('success', PaymentStatus::SUCCESS->color());
        $this->assertEquals('danger', PaymentStatus::FAILED->color());
        $this->assertEquals('gray', PaymentStatus::EXPIRED->color());
    }

    /** @test */
    public function it_checks_if_status_is_success()
    {
        $this->assertTrue(PaymentStatus::SUCCESS->isSuccess());
        $this->assertFalse(PaymentStatus::PENDING->isSuccess());
        $this->assertFalse(PaymentStatus::FAILED->isSuccess());
    }

    /** @test */
    public function it_checks_if_status_is_pending()
    {
        $this->assertTrue(PaymentStatus::PENDING->isPending());
        $this->assertFalse(PaymentStatus::SUCCESS->isPending());
        $this->assertFalse(PaymentStatus::FAILED->isPending());
    }

    /** @test */
    public function it_checks_if_status_is_failed()
    {
        $this->assertTrue(PaymentStatus::FAILED->isFailed());
        $this->assertTrue(PaymentStatus::EXPIRED->isFailed());
        $this->assertTrue(PaymentStatus::CANCELED->isFailed());
        $this->assertFalse(PaymentStatus::SUCCESS->isFailed());
        $this->assertFalse(PaymentStatus::PENDING->isFailed());
    }

    /** @test */
    public function it_returns_options_array()
    {
        $options = PaymentStatus::options();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('pending', $options);
        $this->assertArrayHasKey('success', $options);
        $this->assertEquals('Pending', $options['pending']);
        $this->assertEquals('Success', $options['success']);
    }
}
