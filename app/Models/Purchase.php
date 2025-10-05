<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    // 既存テーブルが "purchases" なら指定不要（Laravelが自動で推測）
    // protected $table = 'purchases';

    protected $fillable = [
        'user_id',
        'item_id',
        // 'payment_method', 'status', ... 必要に応じて
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
