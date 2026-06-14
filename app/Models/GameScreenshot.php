<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameScreenshot extends Model
{
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
