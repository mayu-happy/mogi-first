<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Item;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(StoreCommentRequest $request, Item $item)
    {
        $validated = $request->validated();
        $item->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $validated['body'],
        ]);

        return redirect()
            ->route('items.show', ['item' => $item->id])
            ->with('message', 'コメントを投稿しました。');
    }
}
