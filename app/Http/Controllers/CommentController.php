<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Item, Comment};

class CommentController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:255'], // 256文字でエラー
        ]);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'body'    => $data['body'],
        ]);

        return back();
    }
}
