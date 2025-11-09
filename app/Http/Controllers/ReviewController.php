<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\Review;
use App\Traits\hasMediaItem;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use hasMediaItem;

    public function index()
    {
        return Review::with(['mediaItem', 'user'])
            ->where('is_public', true)
            ->latest()
            ->paginate(20);
    }

    public function store(StoreReviewRequest $request)
    {
        $validated = $request->validated();

        $mediaItem = $this->mediaFetchOrCreate($validated['tmdb_id'], $validated['type']);

        $review = Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'media_item_id' => $mediaItem->id,
            ],
            [
                'rating' => $validated['rating'] ?? null,
                'review_text' => $validated['review_text'] ?? null,
                'is_public' => $validated['is_public'] ?? true,
            ]
        );

        return response()->json($review->load(['mediaItem', 'user']), 201);
    }

    public function show(Review $review)
    {
        return $review->load(['mediaItem', 'user']);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response()->noContent();
    }
}
