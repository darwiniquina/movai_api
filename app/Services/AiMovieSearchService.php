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

        $config = config('services.cerebras');

        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Return only movie, series, or person names in a JSON array. No years, subtitles, or extra text — just clean distinct titles.',
                ],
                [
                    'role' => 'user',
                    'content' => "Give up to {$limit} matches for: \"{$query}\".\nFormat: [\"Title 1\", \"Title 2\", \"Person 1\"]",
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
