<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne, BelongsToMany};
use Illuminate\Database\Eloquent\Builder;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'price',
        'condition',
        'img_url',
        'description',
        'category_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function purchase(): HasOne
    {
        return $this->hasOne(Purchase::class);
    }

    public function likedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes', 'item_id', 'user_id')
            ->withTimestamps();
    }

    public function scopeFavoritedBy(Builder $query, int $userId): Builder
    {
        return $query->whereHas('likedBy', fn($qq) => $qq->where('likes.user_id', $userId));
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_item', 'item_id', 'category_id')
            ->withTimestamps();
    }

    public function getIsSoldAttribute(): bool
    {
        return $this->relationLoaded('purchase')
            ? !is_null($this->purchase)
            : $this->purchase()->exists();
    }
}
