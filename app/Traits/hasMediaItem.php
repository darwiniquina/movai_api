<?php

namespace App\Traits;

use App\Models\MediaItem;
use App\Services\TmdbService;
use Illuminate\Support\Facades\Cache;

trait hasMediaItem
{
    public function mediaFetchOrCreate(int $tmdbId, string $type): MediaItem
    {
        return Cache::remember("media_item_{$tmdbId}", 600, function () use ($tmdbId, $type) {
            $mediaItem = MediaItem::where('tmdb_id', $tmdbId)->first();

            if (! $mediaItem) {
                $details = $type === 'movie'
                    ? app(TmdbService::class)->getMovieDetails($tmdbId)
                    : app(TmdbService::class)->getTvShowDetails($tmdbId);

                $mediaItem = MediaItem::create([
                    'tmdb_id' => $tmdbId,
                    'type' => $type,
                    'title' => $details['title'] ?? 'Unknown',
                    'poster_path' => $details['poster_path'] ?? null,
                    'release_date' => $details['release_date'] ?? null,
                    'overview' => $details['overview'] ?? null,
                ]);
            }

            return $mediaItem;
        });
    }
}
