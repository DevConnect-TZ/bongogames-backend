<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    private const MOBILIPA_API_URL = 'https://api.mobilipa.store';

    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'game_id' => ['required', 'exists:games,id'],
            'buyer_phone' => ['required', 'string', 'min:9', 'max:15'],
        ]);

        $user = $request->user();
        $game = Game::findOrFail($request->game_id);
        $phone = $this->normalizePhone($request->buyer_phone);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'buyer_phone' => $phone,
            'amount' => $game->price,
            'currency' => 'TZS',
            'gateway' => 'mobilipa',
            'payment_status' => 'PENDING',
            'order_id' => 'pending_'.time(),
        ]);

        $apiKey = config('services.mobilipa.api_key');

        $response = Http::withHeaders(['X-API-KEY' => $apiKey])
            ->post(self::MOBILIPA_API_URL.'/v1/payment/create_order', [
                'buyer_email' => $user->email ?? 'customer@bongogames.com',
                'buyer_name' => $user->username,
                'buyer_phone' => $phone,
                'amount' => (int) $game->price,
                'currency' => 'TZS',
            ]);

        if ($response->successful() && ($response['status'] ?? '') === 'success') {
            $data = $response['data'];

            $transaction->update([
                'order_id' => $data['order_id'],
                'reference' => $data['reference'] ?? null,
                'transaction_id' => $data['transid'] ?? null,
                'msisdn' => $data['msisdn'] ?? null,
                'response_data' => $data,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'order_id' => $data['order_id'],
                    'reference' => $data['reference'] ?? null,
                    'amount' => $game->price,
                    'currency' => 'TZS',
                ],
            ]);
        }

        $transaction->update([
            'payment_status' => 'FAILED',
            'response_data' => $response->json(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to initiate payment. Please try again.',
        ], 502);
    }

    public function checkStatus(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_id' => ['required', 'integer', 'exists:transactions,id'],
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);

        if ($transaction->payment_status === 'COMPLETED') {
            return response()->json([
                'status' => 'success',
                'data' => ['payment_status' => 'COMPLETED'],
            ]);
        }

        $apiKey = config('services.mobilipa.api_key');

        $response = Http::withHeaders([
            'X-API-KEY' => $apiKey,
            'Content-Type' => 'application/json',
        ])->withBody(json_encode(['order_id' => $transaction->order_id]), 'application/json')
            ->get(self::MOBILIPA_API_URL.'/v1/payment/status');

        if ($response->successful() && ($response['status'] ?? '') === 'success') {
            $data = $response['data'];
            $status = strtoupper($data['payment_status'] ?? 'PENDING');

            $updateData = [
                'payment_status' => $status,
                'response_data' => $data,
            ];

            if ($status === 'COMPLETED') {
                $updateData['completed_at'] = now();
                $updateData['transaction_id'] = $data['transid'] ?? $transaction->transaction_id;
                $updateData['reference'] = $data['reference'] ?? $transaction->reference;
            }

            $transaction->update($updateData);

            return response()->json([
                'status' => 'success',
                'data' => ['payment_status' => $status],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => ['payment_status' => $transaction->payment_status],
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 9) {
            return '255'.$phone;
        }

        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            return '255'.substr($phone, 1);
        }

        return $phone;
    }
}
