<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Models\Game;
use Illuminate\Http\JsonResponse;

class DeveloperController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $user = request()->user();
        $games = $user->games()->withCount('purchases')->get();

        return response()->json([
            'games_count' => $games->count(),
            'total_sales' => $games->sum('purchases_count'),
            'total_revenue' => $games->sum(fn ($g) => $g->purchases_count * $g->price),
            'games' => $games,
        ]);
    }

    public function games(): JsonResponse
    {
        $games = request()->user()->games()
            ->with(['category', 'screenshots', 'versions'])
            ->latest()
            ->get();

        return response()->json($games);
    }

    public function storeGame(StoreGameRequest $request): JsonResponse
    {
        return app(GameController::class)->store($request);
    }

    public function updateGame(UpdateGameRequest $request, Game $game): JsonResponse
    {
        if ($game->developer_id !== $request->user()->id) {
            abort(403);
        }

        return app(GameController::class)->update($request, $game);
    }
}
