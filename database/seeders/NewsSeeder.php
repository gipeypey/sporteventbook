<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();

        $news = [
            [
                'title' => 'SportEventBook Launches New Platform for Trail Running Events',
                'slug' => 'sporteventbook-launches-new-platform',
                'excerpt' => 'We are excited to announce the launch of our new platform for booking trail running events across the country.',
                'content' => 'SportEventBook is proud to introduce a revolutionary platform designed specifically for trail running enthusiasts. Our new booking system makes it easier than ever to find and register for your favorite events. With partnerships across major event organizers, we bring you the best trail running experiences in one place.',
                'image' => 'news/news-1.jpg',
                'author_id' => $admin?->id,
                'published_at' => now()->subDays(5),
                'is_published' => true,
            ],
            [
                'title' => 'Top 5 Trail Running Destinations in 2026',
                'slug' => 'top-5-trail-running-destinations-2026',
                'excerpt' => 'Discover the most breathtaking trail running destinations you must visit this year.',
                'content' => 'From the mountains of Bali to the forests of Java, Indonesia offers some of the most spectacular trail running experiences. Our top picks include Mount Batur Sunrise Trail, Mount Rinjani Challenge, and the newly opened Bromo Ultra Trail. Each destination offers unique terrain and stunning views that will test your endurance while rewarding you with unforgettable memories.',
                'image' => 'news/news-2.jpg',
                'author_id' => $admin?->id,
                'published_at' => now()->subDays(10),
                'is_published' => true,
            ],
            [
                'title' => 'How to Prepare for Your First Ultra Marathon',
                'slug' => 'how-to-prepare-first-ultra-marathon',
                'excerpt' => 'Expert tips and training advice for runners taking on their first ultra marathon challenge.',
                'content' => 'Training for an ultra marathon requires dedication, proper planning, and mental preparation. Start by building your base mileage gradually, incorporating long runs on weekends, and don\'t forget cross-training for strength and flexibility. Nutrition plays a crucial role - practice your fueling strategy during training runs. Most importantly, listen to your body and allow adequate recovery time between intense training sessions.',
                'image' => 'news/news-3.jpg',
                'author_id' => $admin?->id,
                'published_at' => now()->subDays(15),
                'is_published' => true,
            ],
        ];

        foreach ($news as $item) {
            News::create($item);
        }
    }
}