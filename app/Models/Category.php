<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // 追加！

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            Item::class,    
            'category_item', 
            'category_id',   
            'item_id'        
        )->withTimestamps();
    }
}
