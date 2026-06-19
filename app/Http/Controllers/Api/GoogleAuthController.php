<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Factory;

class GoogleAuthController extends Controller
{
    private FirebaseAuth $firebaseAuth;

    public function __construct()
    {
        $this->firebaseAuth = (new Factory)->withServiceAccount($this->serviceAccountPath())->createAuth();
    }

    public function handle(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string']]);

        try {
            $token = $this->firebaseAuth->verifyIdToken($request->token);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'token' => ['Invalid or expired Firebase token: '.$e->getMessage()],
            ]);
        }

        $firebaseId = $token->claims()->get('sub');
        $email = $token->claims()->get('email');
        $name = $token->claims()->get('name') ?? $this->generateUsername($email);

        $user = User::where('google_id', $firebaseId)->first();

        if (! $user && $email) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $username = $this->generateUniqueUsername($name);

            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email ?: $this->generateEmail($username),
                'phone' => null,
                'password' => Hash::make(uniqid()),
                'google_id' => $firebaseId,
                'auth_provider' => 'google',
                'role' => 'user',
            ]);
        }

        if (! $user->google_id) {
            $user->update([
                'google_id' => $firebaseId,
                'auth_provider' => 'google',
            ]);
        }

        $sanctumToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->only(['id', 'username', 'phone', 'role', 'auth_provider']),
            'token' => $sanctumToken,
        ]);
    }

    private function serviceAccountPath(): string
    {
        $path = storage_path('app/firebase-service-account.json');

        if (! file_exists($path)) {
            throw new \RuntimeException('Firebase service account not found at: '.$path);
        }

        return $path;
    }

    private function generateUsername(?string $email): string
    {
        if (! $email) {
            return 'user'.time();
        }

        return strtolower(preg_replace('/[^a-z0-9_-]/', '', explode('@', $email)[0]));
    }

    private function generateUniqueUsername(string $base): string
    {
        $username = $this->generateUsername($base);
        $original = $username;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $original.$counter;
            $counter++;
        }

        return $username;
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
}
