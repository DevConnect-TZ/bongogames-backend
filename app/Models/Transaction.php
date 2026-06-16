<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'game_id', 'order_id', 'reference', 'buyer_phone',
    'amount', 'currency', 'gateway', 'payment_status',
    'transaction_id', 'channel', 'msisdn', 'response_data', 'completed_at',
])]
class Transaction extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    protected function casts(): array
    {
        return [
            'response_data' => 'array',
            'completed_at' => 'datetime',
        ];
    }
}
