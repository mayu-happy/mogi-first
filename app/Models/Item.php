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
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    // 必要なら guard を緩めるなら: protected $guarded = [];
    protected $fillable = [
        'name',
        'price',
        'brand',
        'description',
        'image_url',
        'condition',
        'user_id',
        'category_id',
        'img_url',
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

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_item', 'item_id', 'category_id');
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

    // 画像URLアクセサ：http(s) 始まりならそのまま、相対なら asset() を付与
    public function getThumbUrlAttribute(): string
    {
        $url = $this->img_url ?: 'images/noimage.png';
        return Str::startsWith($url, ['http://', 'https://']) ? $url : asset($url);
    }
}
