<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TvShowController extends Controller
{
    protected TmdbService $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function popular(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $data = $this->tmdbService->getPopularTvShows($page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch popular TV shows',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function topRated(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $data = $this->tmdbService->getTopRatedTvShows($page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch top rated TV shows',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function airingToday(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $data = $this->tmdbService->getAiringTodayTvShows($page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch airing today TV shows',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function onTheAir(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $data = $this->tmdbService->getOnTheAirTvShows($page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch on the air TV shows',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $data = $this->tmdbService->getTvShowDetails($id);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch TV show details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function season(int $id, int $seasonNumber): JsonResponse
    {
        try {
            $data = $this->tmdbService->getTvSeasonDetails($id, $seasonNumber);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch TV season details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function similar(Request $request, int $id): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $data = $this->tmdbService->getSimilarTvShows($id, $page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch similar TV shows',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'required|string|min:1',
            ]);

            $query = $request->input('query');
            $page = $request->input('page', 1);
            $data = $this->tmdbService->searchTvShows($query, $page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search TV shows',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function discoverByGenre(Request $request, int $genreId): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $additionalParams = $request->only(['sort_by', 'first_air_date_year', 'with_cast', 'with_crew']);

            $data = $this->tmdbService->discoverTvShowsByGenre($genreId, $page, $additionalParams);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to discover TV shows',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
