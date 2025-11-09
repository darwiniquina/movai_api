<?php

namespace App\Http\Controllers;

use App\Http\Requests\Favorite\StoreFavoriteRequest;
use App\Models\Favorite;
use App\Traits\hasMediaItem;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    use hasMediaItem;

    public function index()
    {
        /** @disregard */
        return Auth::user()->favorites()->with('mediaItem')->get();
    }

    public function store(StoreFavoriteRequest $request)
    {
        $validated = $request->validated();

        $mediaItem = $this->mediaFetchOrCreate($validated['tmdb_id'], $validated['type']);

        $favorite = Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'media_item_id' => $mediaItem->id,
        ]);

        return response()->json($favorite->load('mediaItem'), 201);
    }

    public function destroy(Favorite $favorite)
    {
        $favorite->delete();

        return response()->noContent();
    }
}
