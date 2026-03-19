<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdditionalPromoCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Student Discount
        PromoCode::create([
            'code' => 'STUDENT25',
            'name' => 'Student Discount 25%',
            'description' => 'Diskon 25% untuk pelajar mahasiswa',
            'type' => 'percentage',
            'value' => 25,
            'minimum_amount' => 80000,
            'usage_limit' => 150,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addDays(45),
            'is_active' => true,
        ]);

        // Group Booking
        PromoCode::create([
            'code' => 'GROUP100K',
            'name' => 'Group Booking 100K',
            'description' => 'Diskon Rp 100.000 untuk booking grup 5 orang ke atas',
            'type' => 'fixed',
            'value' => 100000,
            'minimum_amount' => 300000,
            'usage_limit' => 75,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addDays(30),
            'is_active' => true,
        ]);

        // Weekend Special
        PromoCode::create([
            'code' => 'WEEKEND15',
            'name' => 'Weekend Special 15%',
            'description' => 'Diskon 15% untuk booking weekend',
            'type' => 'percentage',
            'value' => 15,
            'minimum_amount' => 120000,
            'usage_limit' => 200,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addDays(60),
            'is_active' => true,
        ]);

        // Referral Bonus
        PromoCode::create([
            'code' => 'REFERRAL50',
            'name' => 'Referral Bonus 50K',
            'description' => 'Diskon Rp 50.000 untuk referral dari teman',
            'type' => 'fixed',
            'value' => 50000,
            'minimum_amount' => 100000,
            'usage_limit' => null, // unlimited
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addDays(180),
            'is_active' => true,
        ]);

        // Seasonal Promotion
        PromoCode::create([
            'code' => 'NEWYEAR30',
            'name' => 'New Year Special 30%',
            'description' => 'Diskon spesial tahun baru 30%',
            'type' => 'percentage',
            'value' => 30,
            'minimum_amount' => 200000,
            'usage_limit' => 50,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addDays(14),
            'is_active' => true,
        ]);

        // Loyalty Member
        PromoCode::create([
            'code' => 'LOYALTY10',
            'name' => 'Loyalty Member 10%',
            'description' => 'Diskon loyalitas 10% untuk member setia',
            'type' => 'percentage',
            'value' => 10,
            'minimum_amount' => 50000,
            'usage_limit' => null, // unlimited
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addDays(365),
            'is_active' => true,
        ]);

        // Flash Sale
        PromoCode::create([
            'code' => 'FLASH50',
            'name' => 'Flash Sale 50%',
            'description' => 'Diskon flash sale 50% untuk 24 jam pertama',
            'type' => 'percentage',
            'value' => 50,
            'minimum_amount' => 150000,
            'usage_limit' => 25,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addHours(24),
            'is_active' => true,
        ]);

        // Corporate Discount
        PromoCode::create([
            'code' => 'CORP200K',
            'name' => 'Corporate Discount 200K',
            'description' => 'Diskon khusus perusahaan Rp 200.000',
            'type' => 'fixed',
            'value' => 200000,
            'minimum_amount' => 500000,
            'usage_limit' => 20,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addDays(90),
            'is_active' => true,
        ]);

        // Birthday Special
        PromoCode::create([
            'code' => 'BIRTHDAY20',
            'name' => 'Birthday Special 20%',
            'description' => 'Diskon ulang tahun spesial 20%',
            'type' => 'percentage',
            'value' => 20,
            'minimum_amount' => 100000,
            'usage_limit' => 365,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addDays(30),
            'is_active' => true,
        ]);

        // Premium Member
        PromoCode::create([
            'code' => 'PREMIUM150K',
            'name' => 'Premium Member 150K',
            'description' => 'Diskon premium member Rp 150.000',
            'type' => 'fixed',
            'value' => 150000,
            'minimum_amount' => 250000,
            'usage_limit' => 100,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addDays(120),
            'is_active' => true,
        ]);
    }
}