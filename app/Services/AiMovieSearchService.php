<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiMovieSearchService
{
    public function fetchTitles(string $query, int $limit = 5): array
    {
        if (empty(trim($query))) {
            return [];
        }

        $config = config('services.assemblyai');

        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => <<<'SYS'
You are a helpful assistant that returns only movies, TV series, or people (actors/directors/etc.) in JSON format.
Remove any years, subtitles, or extra text — keep only the clean, main name/title.
Return an array of distinct names/titles that best match the user's description.
SYS,
                ],
                [
                    'role' => 'user',
                    'content' => <<<USR
List up to {$limit} movies, TV series, or people that match this description: "{$query}".
Return a JSON array of clean names/titles only, like:
["Title 1", "Title 2", "Person 1"]
USR,
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => $config['api_key'],
                'Content-Type' => 'application/json',
            ])->post($config['base_url'], [
                'model' => $config['model'],
                'messages' => $messages,
                'temperature' => $config['temperature'],
                'max_tokens' => $config['max_tokens'],
            ]);

            if (! $response->successful()) {
                Log::error('AI fetch failed', ['response' => $response->body()]);

                return [];
            }

            $text = data_get($response->json(), 'choices.0.message.content');
            $titles = json_decode($text, true);

            return collect($titles)
                ->map(fn ($t) => trim((string) $t))
                ->filter()
                ->unique()
                ->values()
                ->toArray();

        } catch (\Throwable $e) {
            Log::error('AI movie search error', ['error' => $e->getMessage()]);

            return [];
        }
    }
}
