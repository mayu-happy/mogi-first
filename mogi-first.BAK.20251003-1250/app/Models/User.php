<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile',
        'address',
        'postal_code',
        'image',
        'building'
    ];


    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id')->withTimestamps();
    }

    public function likes()
    {
        return $this->likedItems();
    }


    public function getAvatarUrlAttribute(): string
    {
        $img = $this->image;

        // 画像未設定 → デフォルト
        if (!$img) {
            return asset('images/avatar-default.png');
        }

        // 外部URLはそのまま返す
        if (Str::startsWith($img, ['http://', 'https://'])) {
            return $img;
        }

        // パス正規化（先頭スラッシュ/ storage/ を外す）
        $path = ltrim($img, '/');
        if (Str::startsWith($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        // profile_images/ が無ければ付ける（ファイル名だけ保存されてるケース対策）
        if (!Str::startsWith($path, 'profile_images/')) {
            $path = 'profile_images/' . $path;
        }

        // 実ファイルが無ければデフォルトにフォールバック
        if (!Storage::disk('public')->exists($path)) {
            return asset('images/avatar-default.png');
        }

        // 実ファイルあり → storage配下URL
        return Storage::url($path);
    }
            
    public function setImageAttribute($value): void
    {
        if (is_string($value)) {
            $v = ltrim($value, '/');
            $v = preg_replace('#^(storage/|public/)#', '', $v);
            $this->attributes['image'] = $v;
        } else {
            $this->attributes['image'] = $value;
        }
    }
}
