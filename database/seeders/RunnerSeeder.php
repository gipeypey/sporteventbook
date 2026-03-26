<?php

namespace Database\Seeders;

use App\Models\Runner;
use Illuminate\Database\Seeder;

class RunnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $runners = [
            // Top Men Runners
            [
                'name' => 'Jim Walmsley',
                'photo' => 'runners/jim-walmsley.jpg',
                'country' => 'USA',
                'gender' => 'men',
                'utmb_index_20k' => 890,
                'utmb_index_50k' => 920,
                'utmb_index_100k' => 952,
                'utmb_index_100m' => 965,
                'is_active' => true,
            ],
            [
                'name' => 'Elhousine Elazzaoui',
                'photo' => 'runners/elhousine-elazzaoui.jpg',
                'country' => 'MAR',
                'gender' => 'men',
                'utmb_index_20k' => 880,
                'utmb_index_50k' => 910,
                'utmb_index_100k' => 944,
                'utmb_index_100m' => 950,
                'is_active' => true,
            ],
            [
                'name' => 'Patrick Kipngeno',
                'photo' => 'runners/patrick-kipngeno.jpg',
                'country' => 'KEN',
                'gender' => 'men',
                'utmb_index_20k' => 875,
                'utmb_index_50k' => 905,
                'utmb_index_100k' => 944,
                'utmb_index_100m' => 948,
                'is_active' => true,
            ],
            // Top Women Runners
            [
                'name' => 'Tove Alexandersson',
                'photo' => 'runners/tove-alexandersson.jpg',
                'country' => 'SWE',
                'gender' => 'women',
                'utmb_index_20k' => 820,
                'utmb_index_50k' => 840,
                'utmb_index_100k' => 853,
                'utmb_index_100m' => 860,
                'is_active' => true,
            ],
            [
                'name' => 'Courtney Dauwalter',
                'photo' => 'runners/courtney-dauwalter.jpg',
                'country' => 'USA',
                'gender' => 'women',
                'utmb_index_20k' => 810,
                'utmb_index_50k' => 825,
                'utmb_index_100k' => 837,
                'utmb_index_100m' => 845,
                'is_active' => true,
            ],
            [
                'name' => 'Katie Schide',
                'photo' => 'runners/katie-schide.jpg',
                'country' => 'USA',
                'gender' => 'women',
                'utmb_index_20k' => 800,
                'utmb_index_50k' => 820,
                'utmb_index_100k' => 835,
                'utmb_index_100m' => 840,
                'is_active' => true,
            ],
        ];

        foreach ($runners as $runner) {
            Runner::create($runner);
        }
    }
}