<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Venue::create([
            'name' => 'Bunder HI Kelapa Tiga',
            'address' => 'Jl. Sudirman, Jakarta Pusat',
            'postal_code' => '19038481'
        ]);

        Venue::create([
            'name' => 'Gelora Bung Karno',
            'address' => 'Jl. Pintu Satu Senayan, Jakarta Selatan',
            'postal_code' => '12190'
        ]);

        Venue::create([
            'name' => 'Tennis Indoor Senayan',
            'address' => 'Jl. Asia Afrika, Jakarta Pusat',
            'postal_code' => '10270'
        ]);
    }
}
