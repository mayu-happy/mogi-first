<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Item;

class LikeController extends Controller
{
    public function toggle(Item $item)
    {
        $user = auth()->user();

        // 既にいいね済みなら外す、なければ付ける
        if ($item->likedBy()->where('users.id', $user->id)->exists()) {
            $item->likedBy()->detach($user->id);
        } else {
            $item->likedBy()->attach($user->id);
        }

        return back();
    }
}
