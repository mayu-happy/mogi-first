<?php

namespace App\Http\Controllers;


use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ItemLikeController extends Controller
{
    public function toggle(Request $request, Item $item)
    {
        if (!Schema::hasTable('likes')) {
            return back()->withErrors(['like' => '現在、いいね機能は有効化されていません。']);
        }

        $user = $request->user();

        $exists = $item->likedBy()->whereKey($user->id)->exists();

        if ($exists) {
            $item->likedBy()->detach($user->id);
            $status = 'removed';
        } else {
            $item->likedBy()->attach($user->id);
            $status = 'added';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => $status,
                'count'  => $item->likedBy()->count(),
            ]);
        }

        return back();
    }
}
