<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Transaction;
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

    public function completePurchase(Transaction $transaction): JsonResponse
    {
        $user = request()->user();

        if ($transaction->user_id !== $user->id) {
            abort(403);
        }

        if ($transaction->payment_status !== 'COMPLETED') {
            return response()->json(['message' => 'Payment not yet completed.'], 422);
        }

        $alreadyOwned = Purchase::where('user_id', $user->id)
            ->where('game_id', $transaction->game_id)
            ->exists();

        if ($alreadyOwned) {
            return response()->json(['message' => 'You already own this game.'], 422);
        }

        Purchase::create([
            'user_id' => $user->id,
            'game_id' => $transaction->game_id,
            'price_paid' => $transaction->amount,
        ]);

        return response()->json(['message' => 'Game purchased successfully.'], 201);
    }
}
