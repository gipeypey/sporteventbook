<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $runningCategory = EventCategory::where('name', 'Running')->first();
        $swimmingCategory = EventCategory::where('name', 'Swimming')->first();
        $boxingCategory = EventCategory::where('name', 'Boxing')->first();
        $tennisCategory = EventCategory::where('name', 'Tennis')->first();
        $powerliftingCategory = EventCategory::where('name', 'Powerlifting')->first();

        $venue1 = Venue::where('name', 'Bunder HI Kelapa Tiga')->first();
        $venue2 = Venue::where('name', 'Gelora Bung Karno')->first();
        $venue3 = Venue::where('name', 'Tennis Indoor Senayan')->first();

        Event::create([
            'title' => 'Fun Run Asia Globally Menolak Menua Sejak Dini',
            'description' => 'Hadiri tantangan 10KM terbaru yang akan bermanfaat diri kamu menjadi lebih dari versi awal agar semakin sukses karir masa depanmu',
            'image' => 'assets/images/run.png',
            'is_featured' => true,
            'category_id' => $runningCategory->id,
            'venue_id' => $venue1->id,
            'date' => '2024-08-18 08:00:00',
            'max_participants' => 30,
            'total_prize' => 120000000,
            'price' => 8500000,
            'status' => 'open'
        ]);

        Event::create([
            'title' => 'Quick Butterfly Sprint in Summer Golden',
            'description' => 'Kompetisi renang butterfly sprint di musim panas',
            'image' => 'assets/images/boys-boxing.png',
            'is_featured' => true,
            'category_id' => $swimmingCategory->id,
            'venue_id' => $venue2->id,
            'date' => '2024-09-15 09:00:00',
            'max_participants' => 50,
            'total_prize' => 50000000,
            'price' => 12000000,
            'status' => 'closed'
        ]);

        Event::create([
            'title' => 'Calling Strong Man Deadlift Power 100lbs',
            'description' => 'Kompetisi powerlifting deadlift untuk pria kuat',
            'image' => 'assets/images/women-tennis.png',
            'is_featured' => true,
            'category_id' => $powerliftingCategory->id,
            'venue_id' => $venue2->id,
            'date' => '2024-07-20 10:00:00',
            'max_participants' => 20,
            'total_prize' => 50000000,
            'price' => 5000000,
            'status' => 'ended',
            'winner_name' => 'Angga Risky',
            'winner_number' => '192'
        ]);

        Event::create([
            'title' => 'Mini Super Fast Game Two Point Ball Match',
            'description' => 'Pertandingan tennis cepat dengan sistem 2 poin',
            'image' => 'assets/images/run.png',
            'is_featured' => true,
            'category_id' => $tennisCategory->id,
            'venue_id' => $venue3->id,
            'date' => '2024-10-10 14:00:00',
            'max_participants' => 16,
            'total_prize' => 25000000,
            'price' => 12000000,
            'status' => 'open'
        ]);

        Event::create([
            'title' => 'Ultra Marathon 42KM Challenge',
            'description' => 'Marathon ultra untuk atlet berpengalaman',
            'image' => 'assets/images/boys-boxing.png',
            'is_featured' => true,
            'category_id' => $runningCategory->id,
            'venue_id' => $venue1->id,
            'date' => '2024-11-15 06:00:00',
            'max_participants' => 100,
            'total_prize' => 200000000,
            'price' => 15000000,
            'status' => 'open'
        ]);

        Event::create([
            'title' => 'Swimming Relay Championship',
            'description' => 'Kompetisi renang estafet 4x100m',
            'image' => 'assets/images/women-tennis.png',
            'is_featured' => true,
            'category_id' => $swimmingCategory->id,
            'venue_id' => $venue2->id,
            'date' => '2024-12-20 10:00:00',
            'max_participants' => 32,
            'total_prize' => 80000000,
            'price' => 10000000,
            'status' => 'closed'
        ]);

        Event::create([
            'title' => 'Gelut Dengan Boxing Jagoan Kota Besar',
            'description' => 'Kompetisi boxing antar jagoan kota besar',
            'category_id' => $boxingCategory->id,
            'venue_id' => $venue1->id,
            'date' => '2024-11-05 19:00:00',
            'max_participants' => 24,
            'total_prize' => 15000000,
            'price' => 5000000,
            'status' => 'closed'
        ]);
    }
}
