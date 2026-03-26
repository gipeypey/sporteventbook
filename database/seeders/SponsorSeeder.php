<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sponsors = [
            // Tier 1 Sponsors (Main Sponsors)
            [
                'name' => 'HOKA',
                'logo' => 'sponsors/hoka.png',
                'url' => 'https://www.hoka.com',
                'tier' => 1,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'SUUNTO',
                'logo' => 'sponsors/suunto.png',
                'url' => 'https://www.suunto.com',
                'tier' => 1,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Salomon',
                'logo' => 'sponsors/salomon.png',
                'url' => 'https://www.salomon.com',
                'tier' => 1,
                'is_active' => true,
                'order' => 3,
            ],
            // Tier 2 Sponsors
            [
                'name' => 'AONIJIE',
                'logo' => 'sponsors/aonijie.png',
                'url' => 'https://www.aonijie.com',
                'tier' => 2,
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'BUFF',
                'logo' => 'sponsors/buff.png',
                'url' => 'https://www.buff.com',
                'tier' => 2,
                'is_active' => true,
                'order' => 5,
            ],
            [
                'name' => 'COMPRESSPORT',
                'logo' => 'sponsors/compressport.png',
                'url' => 'https://www.compressport.com',
                'tier' => 2,
                'is_active' => true,
                'order' => 6,
            ],
            [
                'name' => 'RUDY PROJECT',
                'logo' => 'sponsors/rudy-project.png',
                'url' => 'https://www.rudyproject.com',
                'tier' => 2,
                'is_active' => true,
                'order' => 7,
            ],
            [
                'name' => 'SHOKZ',
                'logo' => 'sponsors/shokz.png',
                'url' => 'https://www.shokz.com',
                'tier' => 2,
                'is_active' => true,
                'order' => 8,
            ],
            // Tier 3 Sponsors
            [
                'name' => 'SIDAS',
                'logo' => 'sponsors/sidas.png',
                'url' => 'https://www.sidas.com',
                'tier' => 3,
                'is_active' => true,
                'order' => 9,
            ],
            [
                'name' => 'Vibram',
                'logo' => 'sponsors/vibram.png',
                'url' => 'https://www.vibram.com',
                'tier' => 3,
                'is_active' => true,
                'order' => 10,
            ],
            [
                'name' => 'NIRVANA',
                'logo' => 'sponsors/nirvana.png',
                'url' => 'https://www.nirvana.com',
                'tier' => 3,
                'is_active' => true,
                'order' => 11,
            ],
        ];

        foreach ($sponsors as $sponsor) {
            Sponsor::create($sponsor);
        }
    }
}