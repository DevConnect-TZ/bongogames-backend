<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    public function index(): JsonResponse
    {
        $wishlist = request()->user()->wishlists()
            ->with(['game' => fn ($q) => $q->with('category')])
            ->latest()
            ->get()
            ->pluck('game');

        return response()->json($wishlist);
    }

    public function add(Game $game): JsonResponse
    {
        $user = request()->user();

        $exists = Wishlist::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Game is already in your wishlist.'], 422);
        }

        Wishlist::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        return response()->json(['message' => 'Added to wishlist.'], 201);
    }

    public function remove(Game $game): JsonResponse
    {
        Wishlist::where('user_id', request()->user()->id)
            ->where('game_id', $game->id)
            ->delete();

        return response()->json(['message' => 'Removed from wishlist.']);
    }
}
