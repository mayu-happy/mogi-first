<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;

class CommentController extends Controller   // ← ここが Controller を継承
{
    public function store(Request $request, Item $item)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:255'],
        ]);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'body'    => $data['body'],
        ]);

        return back();
    }
}
