<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\JsonResponse;

class GenreController extends Controller
{
    protected TmdbService $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function movies(): JsonResponse
    {
        try {
            $data = $this->tmdbService->getMovieGenres();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch movie genres',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function tvShows(): JsonResponse
    {
        try {
            $data = $this->tmdbService->getTvGenres();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch TV genres',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
