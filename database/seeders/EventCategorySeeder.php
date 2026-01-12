<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventCategory::create([
            'name' => 'Running',
            'description' => 'Marathon, Fun Run, Sprint',
            'icon' => 'run.png'
        ]);

        EventCategory::create([
            'name' => 'Swimming',
            'description' => 'Freestyle, Butterfly, Breaststroke',
            'icon' => 'swim.png'
        ]);

        EventCategory::create([
            'name' => 'Boxing',
            'description' => 'Amateur Boxing, Professional Boxing',
            'icon' => 'boxing.png'
        ]);

        EventCategory::create([
            'name' => 'Tennis',
            'description' => 'Singles, Doubles, Mixed Doubles',
            'icon' => 'tennis.png'
        ]);

        EventCategory::create([
            'name' => 'Powerlifting',
            'description' => 'Deadlift, Squat, Bench Press',
            'icon' => 'power.png'
        ]);
    }
}
