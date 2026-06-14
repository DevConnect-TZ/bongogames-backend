<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;

class LibraryController extends Controller
{
    public function index(): JsonResponse
    {
        $purchases = request()->user()->purchases()
            ->with(['game' => fn ($q) => $q->with(['category', 'versions' => fn ($q) => $q->latest()->limit(1)])])
            ->latest()
            ->get()
            ->pluck('game');

        return response()->json($purchases);
    }

    public function purchase(Game $game): JsonResponse
    {
        $user = request()->user();

        $alreadyOwned = Purchase::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->exists();

        if ($alreadyOwned) {
            return response()->json(['message' => 'You already own this game.'], 422);
        }

        Purchase::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'price_paid' => $game->price,
        ]);

        return response()->json(['message' => 'Game purchased successfully.'], 201);
    }
}
