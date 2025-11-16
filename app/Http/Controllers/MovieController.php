<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use App\Traits\hasApiError;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    use hasApiError;

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
            return $this->apiError($e->getMessage());
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
            return $this->apiError($e->getMessage());
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
            return $this->apiError($e->getMessage());
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
            return $this->apiError($e->getMessage());
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
            return $this->apiError($e->getMessage());
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
            return $this->apiError($e->getMessage());
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
            return $this->apiError($e->getMessage());
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
            return $this->apiError($e->getMessage());
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
            return $this->apiError($e->getMessage());
        }
    }
}
