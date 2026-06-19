<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class GameController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $games = Game::with(['category', 'developer:id,username'])
            ->where('status', 'approved')
            ->when($request->category, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $request->category)))
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->sort === 'price_asc', fn ($q) => $q->orderBy('price'))
            ->when($request->sort === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($request->sort === 'rating' || ! $request->sort, fn ($q) => $q->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'))
            ->when($request->sort === 'newest', fn ($q) => $q->latest())
            ->paginate(20);

        return response()->json($games);
    }

    public function store(StoreGameRequest $request): JsonResponse
    {
        $game = Game::create([
            'title' => $request->title,
            'developer_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'description' => $request->description,
            'trailer_url' => $request->trailer_url,
            'status' => 'pending',
        ]);

        if ($request->hasFile('cover')) {
            $game->cover_path = $this->storeOptimizedImage($request->file('cover'), 'covers', 800, 450);
        }

        if ($request->hasFile('thumbnail')) {
            $game->thumbnail_path = $this->storeOptimizedImage($request->file('thumbnail'), 'thumbnails', 400, 300);
        }

        if ($request->hasFile('trailer_file')) {
            $game->trailer_path = $request->file('trailer_file')->store('trailers', 'public');
        }

        $game->save();

        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $i => $file) {
                $game->screenshots()->create([
                    'path' => $this->storeOptimizedImage($file, 'screenshots', 1280, 720),
                    'order' => $i,
                ]);
            }
        }

        $game->versions()->create([
            'version' => $request->version,
            'changelog' => $request->changelog,
            'download_path' => $request->download_link,
        ]);

        return response()->json($game->load(['category', 'screenshots', 'versions']), 201);
    }

    public function show(Game $game): JsonResponse
    {
        $game->load(['category', 'developer:id,username,bio', 'screenshots', 'versions', 'reviews.user:id,username']);

        return response()->json($game);
    }

    public function update(UpdateGameRequest $request, Game $game): JsonResponse
    {
        $game->fill($request->only(['title', 'category_id', 'price', 'description', 'trailer_url']));

        if ($request->hasFile('cover')) {
            if ($game->cover_path) {
                Storage::disk('public')->delete($game->cover_path);
            }
            $game->cover_path = $this->storeOptimizedImage($request->file('cover'), 'covers', 800, 450);
        }

        if ($request->hasFile('thumbnail')) {
            if ($game->thumbnail_path) {
                Storage::disk('public')->delete($game->thumbnail_path);
            }
            $game->thumbnail_path = $this->storeOptimizedImage($request->file('thumbnail'), 'thumbnails', 400, 300);
        }

        if ($request->hasFile('trailer_file')) {
            if ($game->trailer_path) {
                Storage::disk('public')->delete($game->trailer_path);
            }
            $game->trailer_path = $request->file('trailer_file')->store('trailers', 'public');
        }

        $game->save();

        if ($request->hasFile('screenshots')) {
            foreach ($game->screenshots as $screenshot) {
                Storage::disk('public')->delete($screenshot->path);
            }
            $game->screenshots()->delete();

            foreach ($request->file('screenshots') as $i => $file) {
                $game->screenshots()->create([
                    'path' => $this->storeOptimizedImage($file, 'screenshots', 1280, 720),
                    'order' => $i,
                ]);
            }
        }

        if ($request->has('version')) {
            $game->versions()->create([
                'version' => $request->version,
                'changelog' => $request->changelog,
                'download_path' => $request->download_link,
            ]);
        }

        return response()->json($game->load(['category', 'screenshots', 'versions']));
    }

    public function destroy(Game $game): JsonResponse
    {
        if ($game->cover_path) {
            Storage::disk('public')->delete($game->cover_path);
        }
        if ($game->thumbnail_path) {
            Storage::disk('public')->delete($game->thumbnail_path);
        }
        if ($game->trailer_path) {
            Storage::disk('public')->delete($game->trailer_path);
        }
        foreach ($game->screenshots as $screenshot) {
            Storage::disk('public')->delete($screenshot->path);
        }

        $game->delete();

        return response()->json(['message' => 'Game deleted.']);
    }

    private function storeOptimizedImage(UploadedFile $file, string $folder, int $maxWidth, int $maxHeight): string
    {
        $image = Image::read($file);

        if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
            $image->scaleDown($maxWidth, $maxHeight);
        }

        $path = $folder.'/'.uniqid().'.webp';

        Storage::disk('public')->put($path, $image->toWebp(quality: 80));

        return $path;
    }
}
