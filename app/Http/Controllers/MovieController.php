<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
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
            $data = $this->tmdbService->getPopularMovies($page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            dd($e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch popular movies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function topRated(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $data = $this->tmdbService->getTopRatedMovies($page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch top rated movies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function upcoming(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $data = $this->tmdbService->getUpcomingMovies($page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch upcoming movies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function nowPlaying(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $data = $this->tmdbService->getNowPlayingMovies($page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch now playing movies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $data = $this->tmdbService->getMovieDetails($id);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch movie details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function similar(Request $request, int $id): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $data = $this->tmdbService->getSimilarMovies($id, $page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch similar movies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function recommendations(Request $request, int $id): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $data = $this->tmdbService->getMovieRecommendations($id, $page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch movie recommendations',
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
            $data = $this->tmdbService->searchMovies($query, $page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search movies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function discoverByGenre(Request $request, int $genreId): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $additionalParams = $request->only(['sort_by', 'year', 'with_cast', 'with_crew']);

            $data = $this->tmdbService->discoverMoviesByGenre($genreId, $page, $additionalParams);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to discover movies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
