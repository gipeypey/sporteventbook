<?php

namespace Tests\Feature;

use App\Models\PromoCode;
use Carbon\Carbon;
use Tests\TestCase;

class PromoCodeTest extends TestCase
{
    /** @test */
    public function it_validates_active_promo_code()
    {
        $promo = PromoCode::create([
            'code' => 'PROMO10',
            'name' => '10% Discount',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);

        $this->assertTrue($promo->isValid(100));
    }

    /** @test */
    public function it_rejects_inactive_promo_code()
    {
        $promo = PromoCode::create([
            'code' => 'INACTIVE',
            'name' => 'Inactive Promo',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => false,
        ]);

        $this->assertFalse($promo->isValid(100));
    }

    /** @test */
    public function it_rejects_promo_before_start_date()
    {
        $promo = PromoCode::create([
            'code' => 'FUTURE',
            'name' => 'Future Promo',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'starts_at' => Carbon::now()->addDays(7),
        ]);

        $this->assertFalse($promo->isValid(100));
    }

    /** @test */
    public function it_rejects_promo_after_expiry_date()
    {
        $promo = PromoCode::create([
            'code' => 'EXPIRED',
            'name' => 'Expired Promo',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'expires_at' => Carbon::now()->subDays(1),
        ]);

        $this->assertFalse($promo->isValid(100));
    }

    /** @test */
    public function it_rejects_promo_if_usage_limit_reached()
    {
        $promo = PromoCode::create([
            'code' => 'LIMITED',
            'name' => 'Limited Promo',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'usage_limit' => 100,
            'used_count' => 100,
        ]);

        $this->assertFalse($promo->isValid(100));
    }

    /** @test */
    public function it_rejects_promo_if_minimum_amount_not_met()
    {
        $promo = PromoCode::create([
            'code' => 'MIN50',
            'name' => 'Minimum 50 Promo',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'minimum_amount' => 50000,
        ]);

        $this->assertFalse($promo->isValid(10000));
        $this->assertTrue($promo->isValid(100000));
    }

    /** @test */
    public function it_calculates_percentage_discount()
    {
        $promo = PromoCode::create([
            'code' => 'PERCENT10',
            'name' => '10 Percent',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);

        $discount = $promo->calculateDiscount(100000);
        $this->assertEquals(10000, $discount);
    }

    /** @test */
    public function it_calculates_fixed_discount()
    {
        $promo = PromoCode::create([
            'code' => 'FIXED50K',
            'name' => '50K Fixed',
            'type' => 'fixed',
            'value' => 50000,
            'is_active' => true,
        ]);

        $discount = $promo->calculateDiscount(100000);
        $this->assertEquals(50000, $discount);
    }

    /** @test */
    public function it_caps_fixed_discount_at_order_total()
    {
        $promo = PromoCode::create([
            'code' => 'FIXED100K',
            'name' => '100K Fixed',
            'type' => 'fixed',
            'value' => 100000,
            'is_active' => true,
        ]);

        $discount = $promo->calculateDiscount(50000);
        $this->assertEquals(50000, $discount); // Capped at order total
    }

    /** @test */
    public function it_increments_usage_count()
    {
        $promo = PromoCode::create([
            'code' => 'USAGE',
            'name' => 'Usage Test',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'used_count' => 5,
        ]);

        $promo->incrementUsage();
        
        $this->assertEquals(6, $promo->fresh()->used_count);
    }
}
