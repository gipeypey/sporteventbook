<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventPrize;

class EventPrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the fun run event
        $funRunEvent = Event::where('slug', 'fun-run-asia-globally-menolak-menua-sejak-dini')->first();

        if ($funRunEvent) {
            $prizes = [
                [
                    'event_id' => $funRunEvent->id,
                    'name' => 'BMW M2 CSL',
                    'given_by' => 'by Event Organizer',
                    'image' => null,
                ],
                [
                    'event_id' => $funRunEvent->id,
                    'name' => 'Bag Comodo',
                    'given_by' => 'by Event Organizer',
                    'image' => null,
                ],
                [
                    'event_id' => $funRunEvent->id,
                    'name' => 'Sepatu Ice',
                    'given_by' => 'by Event Organizer',
                    'image' => null,
                ],
            ];

            foreach ($prizes as $prize) {
                EventPrize::create($prize);
            }
        }
    }
}
