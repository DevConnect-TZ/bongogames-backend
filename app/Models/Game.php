<?php

namespace App\Models;

use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory;

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function screenshots(): HasMany
    {
        return $this->hasMany(GameScreenshot::class)->orderBy('order');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(GameVersion::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
