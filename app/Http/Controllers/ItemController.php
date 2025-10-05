<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $tab = $request->query('tab') === 'mylist' ? 'mylist' : 'recommend';
        $kw  = trim((string) $request->query('q', ''));

        $query = \App\Models\Item::query()
            ->with(['user'])
            ->withCount(['comments', 'likedBy'])
            ->latest('id');

        // ← name だけで部分一致
        if ($kw !== '') {
            $query->where('name', 'like', "%{$kw}%");
        }

        if ($tab === 'mylist') {
            if (!auth()->check()) {
                return redirect()->route('login')->with('status', 'マイリストを見るにはログインが必要です。');
            }
            $query->favoritedBy(auth()->id());
        }

        $items = $query->paginate(12)->withQueryString();
        return view('items.index', compact('items', 'tab', 'kw'));
    }

    public function show(\App\Models\Item $item)
    {
        $item->load([
            'user',
            'comments.user',
            'purchase',
            'likedBy',
        ])->loadCount(['comments', 'likedBy']);

        $liked = auth()->check() ? $item->likedBy->contains(auth()->id()) : false;

        return view('items.show', compact('item', 'liked'));
    }
}
