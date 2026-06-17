<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreDeveloperRequest;
use App\Http\Requests\AdminStoreUserRequest;
use App\Http\Requests\AdminUpdateDeveloperRequest;
use App\Http\Requests\AdminUpdateUserRequest;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Requests\UpdateGameStatusRequest;
use App\Models\Game;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'total_games' => Game::count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_developers' => User::where('role', 'developer')->count(),
            'total_sales' => Purchase::count(),
            'total_revenue' => Purchase::sum('price_paid'),
        ]);
    }

    public function games(): JsonResponse
    {
        $games = Game::with(['category', 'developer:id,username'])
            ->withCount(['purchases', 'reviews'])
            ->latest()
            ->paginate(20);

        return response()->json($games);
    }

    public function storeGame(StoreGameRequest $request): JsonResponse
    {
        return app(GameController::class)->store($request);
    }

    public function updateGame(UpdateGameRequest $request, Game $game): JsonResponse
    {
        return app(GameController::class)->update($request, $game);
    }

    public function updateGameStatus(UpdateGameStatusRequest $request, Game $game): JsonResponse
    {
        $game->update(['status' => $request->status]);

        return response()->json($game);
    }

    public function deleteGame(Game $game): JsonResponse
    {
        return app(GameController::class)->destroy($game);
    }

    public function users(): JsonResponse
    {
        $users = User::withCount(['purchases', 'reviews'])
            ->latest()
            ->paginate(20);

        return response()->json($users);
    }

    public function storeUser(AdminStoreUserRequest $request): JsonResponse
    {
        $email = $request->email ?: $this->generateEmail($request->username);

        $user = User::create([
            'name' => $request->username,
            'username' => $request->username,
            'email' => $email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => $request->role,
            'bio' => $request->bio,
        ]);

        return response()->json($user, 201);
    }

    private function generateEmail(string $username): string
    {
        $base = strtolower(preg_replace('/[^a-z0-9_-]/', '', $username));

        if ($base === '') {
            $base = 'user'.time();
        }

        $email = $base.'@bongogames.local';

        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $base.'.'.$counter.'@bongogames.local';
            $counter++;
        }

        return $email;
    }

    public function updateUser(AdminUpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['username'])) {
            $data['name'] = $data['username'];
        }

        if (isset($data['password'])) {
            $data['password'] = $data['password'];
        }

        $user->update($data);

        return response()->json($user);
    }

    public function deleteUser(User $user): JsonResponse
    {
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot delete admin users.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }

    public function developers(): JsonResponse
    {
        $developers = User::where('role', 'developer')
            ->withCount('games')
            ->latest()
            ->paginate(20);

        return response()->json($developers);
    }

    public function storeDeveloper(AdminStoreDeveloperRequest $request): JsonResponse
    {
        $email = $request->email ?: $this->generateEmail($request->username);

        $developer = User::create([
            'name' => $request->username,
            'username' => $request->username,
            'email' => $email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => 'developer',
            'bio' => $request->bio,
        ]);

        return response()->json($developer, 201);
    }

    public function updateDeveloper(AdminUpdateDeveloperRequest $request, User $developer): JsonResponse
    {
        if ($developer->role !== 'developer') {
            return response()->json(['message' => 'User is not a developer.'], 422);
        }

        $data = $request->validated();

        if (isset($data['username'])) {
            $data['name'] = $data['username'];
        }

        $developer->update($data);

        return response()->json($developer);
    }

    public function deleteDeveloper(User $developer): JsonResponse
    {
        if ($developer->role !== 'developer') {
            return response()->json(['message' => 'User is not a developer.'], 422);
        }

        $developer->delete();

        return response()->json(['message' => 'Developer deleted.']);
    }

    public function sales(): JsonResponse
    {
        $sales = Purchase::with(['user:id,username', 'game:id,title,price'])
            ->latest()
            ->paginate(20);

        return response()->json($sales);
    }
}
