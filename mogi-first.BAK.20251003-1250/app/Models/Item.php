<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'brand',
        'description',
        'image_url',
        'condition',
        'user_id',
        'category_id',
        'img_url'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
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

    public function likes(): BelongsToMany
    {
        return $this->likedBy();
    }

    public function favorites(): BelongsToMany
    {
        return $this->likedBy();
    }

    public function scopeExceptOwner(Builder $q, ?int $userId = null): Builder
    {
        $userId ??= Auth::id();
        if (!$userId) return $q;

        return $q->where(function ($q) use ($userId) {
            $q->whereHas('user', fn($uq) => $uq->whereKeyNot($userId))
                ->orWhereDoesntHave('user');
        });
    }

    public function categories()
    {
        return $this->belongsToMany(\App\Models\Category::class, 'category_item', 'item_id', 'category_id');
    }
    
    public function getThumbUrlAttribute(): string
    {
        return $this->img_url ? asset($this->img_url) : asset('images/noimage.png');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
