<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FaqArticle;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (FaqArticle::query()->count() === 0) {
            FaqArticle::create([
                'slug' => 'check-in-time',
                'title' => 'What is the check-in time?',
                'content' => 'Check-in starts at 3 PM local time. Early check-in is subject to availability.',
                'locale' => 'en',
                'category' => 'general',
                'tags' => ['check-in', 'time'],
            ]);
            FaqArticle::create([
                'slug' => 'wifi-availability',
                'title' => 'Is Wi-Fi available?',
                'content' => 'Complimentary high-speed Wi-Fi is available throughout the property.',
                'locale' => 'en',
                'category' => 'amenities',
                'tags' => ['wifi', 'internet'],
            ]);
            FaqArticle::create([
                'slug' => 'parking',
                'title' => 'Do you offer parking?',
                'content' => 'Secure on-site parking is available for guests. Fees may apply depending on location.',
                'locale' => 'en',
                'category' => 'transport',
                'tags' => ['parking', 'car'],
            ]);
        }
    }
}
