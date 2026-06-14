<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Game;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function index(Game $game): JsonResponse
    {
        $reviews = $game->reviews()->with('user:id,username')->latest()->paginate(20);

        return response()->json($reviews);
    }

    public function store(StoreReviewRequest $request, Game $game): JsonResponse
    {
        $existing = Review::where('user_id', $request->user()->id)
            ->where('game_id', $game->id)
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'You have already reviewed this game.'], 422);
        }

        $review = $game->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'text' => $request->text,
        ]);

        return response()->json($review->load('user:id,username'), 201);
    }
}
