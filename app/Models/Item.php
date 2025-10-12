<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne, BelongsToMany};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        'user_id',
    ];

    protected $appends = ['img_src'];

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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_item', 'item_id', 'category_id')
            ->withTimestamps();
    }

    public function scopeFavoritedBy(Builder $query, int $userId): Builder
    {
        return $query->whereHas('likedBy', fn($qq) => $qq->where('likes.user_id', $userId));
    }

    public function getIsSoldAttribute(): bool
    {
        return $this->relationLoaded('purchase')
            ? ($this->purchase !== null)
            : $this->purchase()->exists();
    }

    public function getImageUrlAttribute(): ?string
    {
        $raw = $this->img_url;

        if (empty($raw)) {
            $raw = $this->relationLoaded('mainImage')
                ? optional($this->mainImage)->path
                : $this->mainImage()->value('path');

            if (empty($raw)) {
                $raw = $this->relationLoaded('images')
                    ? optional($this->images->first())->path
                    : $this->images()->orderBy('id')->value('path');
            }

            if (empty($raw)) return null;
        }

        if (Str::startsWith($raw, ['http://', 'https://'])) return $raw;

        $rel = ltrim($raw, '/');
        if (Str::startsWith($rel, 'storage/')) $rel = Str::after($rel, 'storage/');
        if (Str::startsWith($rel, 'public/'))  $rel = Str::after($rel, 'public/');

        if (!Storage::disk('public')->exists($rel)) return null;

        return asset('storage/' . $rel);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ItemImage::class)->orderBy('id');
    }

    public function mainImage(): HasOne
    {
        return $this->hasOne(ItemImage::class)->where('is_main', true);
    }

    public function scopeExceptMine(Builder $q, ?int $userId): Builder
    {
        if (!$userId) return $q;
        return $q->where('items.user_id', '!=', $userId);
    }

    public function getImgSrcAttribute(): string
    {
        return $this->image_url ?? asset('images/noimage.png');
    }
}
