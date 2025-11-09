<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function user(Request $request): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    public function search(Request $request)
    {
        $query = trim($request->input('query', ''));

        if (strlen($query) < 2) {
            return response()->json(['message' => 'Search query too short.'], 422);
        }

        $users = User::query()
            ->where('username', 'like', "%{$query}%")
            ->orWhere('display_name', 'like', "%{$query}%")
            ->select('id', 'username', 'display_name', 'bio', 'created_at')
            ->limit(20)
            ->get();

        return response()->json([
            'query' => $query,
            'results' => $users,
        ]);
    }

    public function show(User $user)
    {
        $user->load([
            'watchlistItems.mediaItem:id,tmdb_id,title,poster_path,type',
            'favorites.mediaItem:id,tmdb_id,title,poster_path,type',
            'reviews.mediaItem:id,tmdb_id,title,poster_path,type',
        ]);

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'display_name' => $user->display_name,
            'bio' => $user->bio,
            'joined_at' => $user->created_at->toDateString(),
            'watchlist' => $user->watchlistItems,
            'favorites' => $user->favorites,
            'reviews' => $user->reviews,
        ]);
    }
}
