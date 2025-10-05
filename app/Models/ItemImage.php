<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    protected $fillable = ['item_id', 'path', 'is_main'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}