<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Early Bird Discount
        PromoCode::create([
            'code' => 'EARLYBIRD20',
            'name' => 'Early Bird Discount',
            'description' => 'Diskon 20% untuk pendaftaran early bird',
            'type' => 'percentage',
            'value' => 20,
            'minimum_amount' => 100000,
            'usage_limit' => 50,
            'starts_at' => now(),
            'expires_at' => now()->addDays(30),
            'is_active' => true,
        ]);

        // Fixed Discount
        PromoCode::create([
            'code' => 'RUNNER50K',
            'name' => 'Runner Discount 50K',
            'description' => 'Diskon Rp 50.000 untuk semua pelari',
            'type' => 'fixed',
            'value' => 50000,
            'minimum_amount' => 150000,
            'usage_limit' => 100,
            'starts_at' => now(),
            'expires_at' => now()->addDays(60),
            'is_active' => true,
        ]);

        // First Time Runner
        PromoCode::create([
            'code' => 'FIRSTTIME',
            'name' => 'First Time Runner',
            'description' => 'Diskon khusus untuk pelari pertama kali',
            'type' => 'percentage',
            'value' => 15,
            'minimum_amount' => 0,
            'usage_limit' => null, // unlimited
            'starts_at' => now(),
            'expires_at' => now()->addDays(90),
            'is_active' => true,
        ]);

        // VIP Runner
        PromoCode::create([
            'code' => 'VIPRUNNER',
            'name' => 'VIP Runner',
            'description' => 'Diskon khusus VIP members',
            'type' => 'percentage',
            'value' => 30,
            'minimum_amount' => 200000,
            'usage_limit' => 20,
            'starts_at' => now(),
            'expires_at' => now()->addDays(45),
            'is_active' => true,
        ]);

        // Free Shipping (Insurance)
        PromoCode::create([
            'code' => 'FREESHIP',
            'name' => 'Free Insurance',
            'description' => 'Gratis asuransi untuk booking',
            'type' => 'fixed',
            'value' => 25000,
            'minimum_amount' => 100000,
            'usage_limit' => 200,
            'starts_at' => now(),
            'expires_at' => now()->addDays(120),
            'is_active' => true,
        ]);
    }
}
