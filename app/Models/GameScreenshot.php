<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['game_id', 'path', 'order'])]
class GameScreenshot extends Model
{
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
