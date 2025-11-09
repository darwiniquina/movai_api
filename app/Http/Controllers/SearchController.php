<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected TmdbService $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function multi(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'required|string|min:1',
            ]);

            $query = $request->input('query');
            $page = $request->input('page', 1);
            $data = $this->tmdbService->multiSearch($query, $page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform multi search',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function people(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'required|string|min:1',
            ]);

            $query = $request->input('query');
            $page = $request->input('page', 1);
            $data = $this->tmdbService->searchPeople($query, $page);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search people',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
