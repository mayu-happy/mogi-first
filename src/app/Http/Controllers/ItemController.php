<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab') === 'mylist' ? 'mylist' : 'recommend';
        $q   = trim((string) $request->query('q', ''));

        $query = Item::query()
            ->with([
                'user',
                'purchase' => fn($qq) => $qq->select('id', 'item_id'),
            ])
            ->withExists(['purchase'])
            ->withCount(['comments', 'likedBy'])
            ->latest('id');

        // 検索
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($tab === 'mylist') {
            if (Auth::check()) {
                // 自分の出品を除外したいならこれで正しい
                $query->where('user_id', '!=', Auth::id());

                // 自分がいいねした商品だけ
                $query->whereHas('likedBy', fn($qq) => $qq->whereKey(Auth::id()));

                $items = $query->paginate(24)->withQueryString();
            } else {
                // 未ログイン時は空コレクション
                $items = collect();
            }
        } else {
            // おすすめタブ
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
            $items = $query->paginate(24)->withQueryString();
        }

        return view('items.index', compact('items', 'tab', 'q'));
    }

    public function show(Item $item)
    {
        $item->load(['user', 'categories'])
            ->loadCount(['comments', 'likedBy']);

        $with = ['user'];
        $withCount = [];

        if (Schema::hasTable('comments')) {
            $with[] = 'comments.user';
            $withCount[] = 'comments';
        }
        if (Schema::hasTable('likes')) {
            $with[] = 'likedBy';
            $withCount[] = 'likedBy';
        }
        if (Schema::hasTable('purchases')) {
            $with[] = 'purchase';
        }

        if (!empty($with)) {
            $item->load($with);
        }
        if (!empty($withCount)) {
            $item->loadCount($withCount);
        }

        $liked = false;
        if (Schema::hasTable('likes') && auth()->check()) {
            $liked = $item->relationLoaded('likedBy')
                ? $item->likedBy->contains(auth()->id())
                : $item->likedBy()->whereKey(auth()->id())->exists();
        }

        $isSold = Schema::hasTable('purchases') ? (bool) $item->purchase : false;

        if (Schema::hasTable('comments')) {
            $comments = $item->comments()
                ->with('user')      // ★ カラム絞らない
                ->latest()
                ->paginate(10)
                ->withQueryString();
        } else {
            $comments = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        return view('items.show', compact('item', 'liked', 'isSold', 'comments'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'max:2000'],
            'price'         => ['required', 'integer', 'min:1'],
            'condition'     => ['nullable', 'string'],
            'brand'         => ['nullable', 'string', 'max:255'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'category_ids'   => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]);

        $relPath = null;
        if ($request->hasFile('image')) {
            $relPath = $request->file('image')->store('items', 'public'); // => items/xxxx.jpg
        }

        $item = \App\Models\Item::create([
            'user_id'     => $request->user()->id,
            'name'        => $v['name'],
            'description' => $v['description'],
            'price'       => $v['price'],
            'condition'   => $v['condition'] ?? null,
            'brand'       => $v['brand'] ?? null,
            'img_url'     => $relPath,
        ]);

        foreach (session('draft_images', []) as $path) {
            \App\Models\ItemImage::create(['item_id' => $item->id, 'path' => $path]);
        }
        $request->session()->forget('draft_images');

        $item->categories()->sync($v['category_ids']);

        return redirect()
            ->route('items.index')
            ->with('status', '出品しました！');
    }
}
