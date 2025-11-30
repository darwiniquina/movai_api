<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\WatchlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function user(Request $request): JsonResponse
    {
        $user = Auth::user();

        $watch_list_count = WatchlistItem::where('user_id', $user->id)->get();

        $to_watch_count = $watch_list_count->where('status', 'planning')->count();
        $completed_count = $watch_list_count->where('status', 'completed')->count();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'bio' => $user->bio,
                'public_profile' => $user->public_profile,
                'emoji_avatar' => $user->emoji_avatar,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'to_watch_count' => $to_watch_count,
                'completed_count' => $completed_count,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    public function update(UpdateUserRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();

        $user->name = $validated['name'] ?? $user->name;
        $user->display_name = $validated['display_name'] ?? $user->display_name;
        $user->bio = $validated['bio'] ?? $user->bio;
        $user->public_profile = $validated['public_profile'] ?? $user->public_profile;
        $user->emoji_avatar = $validated['emoji_avatar'] ?? $user->emoji_avatar;

        /**@disregard */
        $user->save();

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'bio' => $user->bio,
                'public_profile' => $user->public_profile,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->password = Hash::make($validated['password']);

        /**@disregard */
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    public function search(Request $request)
    {
        $query = trim($request->input('query', ''));

        if (strlen($query) < 2) {
            return response()->json(['message' => 'Search query too short.'], 422);
        }

        $users = User::query()
            ->where('email', 'like', "%{$query}%")
            ->where('username', 'like', "%{$query}%")
            ->orWhere('display_name', 'like', "%{$query}%")
            ->select('id', 'username', 'display_name', 'bio', 'emoji_avatar', 'created_at')
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
            'emoji_avatar' => $user->emoji_avatar,
            'bio' => $user->bio,
            'joined_at' => $user->created_at->toDateString(),
            'watchlist' => $user->watchlistItems,
            'favorites' => $user->favorites,
            'reviews' => $user->reviews,
        ]);
    }

    public function people(Request $request)
    {
        $users = User::query()
            ->select('id', 'username', 'display_name', 'bio', 'emoji_avatar', 'created_at')
            ->limit(3)
            ->whereNot('id', Auth::user()->id)
            ->get();

        return response()->json([
            'results' => $users,
        ]);
    }
}
