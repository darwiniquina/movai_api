<?php

namespace Database\Seeders;

use App\Models\MediaItem;
use App\Models\User;
use App\Models\WatchlistItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'username' => 'test',
            'email' => 'test@example.com',
            'password' => bcrypt('secret'),
            'email_verified_at' => now(),
        ]);

        $users = [
            [
                'name' => 'Test User',
                'username' => 'john_doe',
                'display_name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'bio' => 'Movie enthusiast and sci-fi lover.',
            ],
            [
                'name' => 'Test User',
                'username' => 'jane_smith',
                'display_name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'bio' => 'Netflix addict and TV show reviewer.',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);

            $mediaItems = [
                [
                    'tmdb_id' => 1,
                    'type' => 'movie',
                    'title' => 'Inception',
                    'poster_path' => '/inception.jpg',
                    'release_date' => '2010-07-16',
                    'overview' => 'A thief who steals corporate secrets through dream-sharing technology...',
                ],
                [
                    'tmdb_id' => 2,
                    'type' => 'tv',
                    'title' => 'Stranger Things',
                    'poster_path' => '/stranger_things.jpg',
                    'release_date' => '2016-07-15',
                    'overview' => 'When a young boy vanishes, a small town uncovers a mystery involving secret experiments...',
                ],
            ];

            foreach ($mediaItems as $item) {
                $mediaItem = MediaItem::firstOrCreate(
                    ['tmdb_id' => $item['tmdb_id']],
                    $item
                );

                WatchlistItem::create([
                    'user_id' => $user->id,
                    'media_item_id' => $mediaItem->id,
                    'status' => 'planning',
                    'notes' => 'Excited to watch!',
                ]);
            }
        }
    }
}
