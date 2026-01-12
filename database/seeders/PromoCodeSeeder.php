<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PromoCode;

class PromoCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promoCodes = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount',
                'description' => '10% discount for new users',
                'type' => 'percentage',
                'value' => 10,
                'minimum_amount' => 100000,
                'usage_limit' => 100,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'MARATHON50',
                'name' => 'Marathon Special',
                'description' => 'Rp 50,000 discount for marathon events',
                'type' => 'fixed',
                'value' => 50000,
                'minimum_amount' => 200000,
                'usage_limit' => 50,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'EARLY20',
                'name' => 'Early Bird',
                'description' => '20% discount for early registration',
                'type' => 'percentage',
                'value' => 20,
                'minimum_amount' => 150000,
                'usage_limit' => 30,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
                'is_active' => true,
            ],
        ];

        foreach ($promoCodes as $promoCode) {
            PromoCode::create($promoCode);
        }
    }
}
