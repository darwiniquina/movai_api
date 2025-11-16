<?php

namespace App\Http\Controllers;

use App\Models\AiUsageLog;
use App\Services\AiMovieSearchService;
use App\Services\TmdbService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AiController extends Controller
{
    public const MAX_LIMIT = 10;

    public function getLimit()
    {
        $log = $this->fetchOrCreateAiLog();

        return response()->json([
            'limit' => self::MAX_LIMIT,
            'used' => $log->count,
            'remaining' => max(self::MAX_LIMIT - $log->count, 0),
        ]);
    }

    public function search(Request $request, TmdbService $tmdb, AiMovieSearchService $ai)
    {
        try {

            DB::beginTransaction();

            $this->checkAiLimit();

            $query = $request->input('query');

            if (! $query) {
                return response()->json(['error' => 'Query required.'], 400);
            }

            $suggestedTitles = $ai->fetchTitles($query);

            $results = collect();

            foreach ($suggestedTitles as $title) {
                $search = $tmdb->multiSearch($title);

                // merge results, taking top 1–2 per title from TMDB
                if (! empty($search['results'])) {
                    $results = $results->merge(array_slice($search['results'] ?? [], 0, 2));
                }
            }

            DB::commit();

            return response()->json([
                'query' => $query,
                'suggested_titles' => $suggestedTitles,
                'results' => $results->values(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    protected function checkAiLimit()
    {
        $log = $this->fetchOrCreateAiLog();

        if ($log->count >= self::MAX_LIMIT) {
            abort(429, 'Daily AI search limit reached. Please try again tomorrow.');
        }

        $log->increment('count');
    }

    protected function fetchOrCreateAiLog()
    {
        return AiUsageLog::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => Carbon::today()],
            ['count' => 0]
        );
    }
}
