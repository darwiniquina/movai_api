<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrendingController extends Controller
{
    protected TmdbService $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Get trending content
     *
     * @param  string  $mediaType  'all', 'movie', 'tv', 'person'
     * @param  string  $timeWindow  'day' or 'week'
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $mediaType = $request->input('media_type', 'all');
            $timeWindow = $request->input('time_window', 'week');

            if (! in_array($mediaType, ['all', 'movie', 'tv', 'person'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid media type. Must be: all, movie, tv, or person',
                ], 400);
            }

            if (! in_array($timeWindow, ['day', 'week'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid time window. Must be: day or week',
                ], 400);
            }

            $data = $this->tmdbService->getTrending($mediaType, $timeWindow);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trending content',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
