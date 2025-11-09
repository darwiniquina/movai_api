<?php

namespace App\Http\Controllers;

use App\Http\Requests\WatchList\StoreWatchlistReqeuest;
use App\Models\WatchlistItem;
use App\Traits\hasMediaItem;
use Illuminate\Support\Facades\Auth;

class WatchlistItemController extends Controller
{
    use hasMediaItem;

    public function index()
    {
        /** @disregard */
        return Auth::user()->watchlistItems()->with('mediaItem')->get();
    }

    public function store(StoreWatchlistReqeuest $request)
    {
        $validated = $request->validated();

        $mediaItem = $this->mediaFetchOrCreate($validated['tmdb_id'], $validated['type']);

        $watchlistItem = WatchlistItem::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'media_item_id' => $mediaItem->id,
            ],
            [
                'status' => $validated['status'] ?? 'planning',
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json($watchlistItem->load('mediaItem'), 201);
    }

    public function destroy(WatchlistItem $watchlistItem)
    {
        $watchlistItem->delete();

        return response()->noContent();
    }
}
