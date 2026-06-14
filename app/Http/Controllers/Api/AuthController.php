<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->username,
            'username' => $request->username,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => 'user',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->only(['id', 'username', 'phone', 'role']),
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->only(['id', 'username', 'phone', 'role']),
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(): JsonResponse
    {
        $user = request()->user();

        return response()->json([
            'user' => $user->only(['id', 'username', 'phone', 'role', 'bio']),
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('username', $request->username)
            ->where('phone', $request->phone)
            ->first();

        if (! $user) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        return response()->json(['message' => 'Account verified. You may now reset your password.']);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('username', $request->username)
            ->where('phone', $request->phone)
            ->first();

        if (! $user) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        $user->update(['password' => $request->password]);

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
