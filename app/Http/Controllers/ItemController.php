<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $tab = $request->query('tab') === 'mylist' ? 'mylist' : 'recommend';
        $kw  = trim((string) $request->query('q', ''));

        $query = Item::query()
            ->with(['user'])
            ->withExists(['purchase'])
            ->withCount(['comments', 'likedBy'])
            ->latest('id');

        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        if ($kw !== '') {
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                    ->orWhere('description', 'like', "%{$kw}%");
            });
        }

        if ($tab === 'mylist') {
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('status', 'マイリストを見るにはログインが必要です。');
            }
            $query->whereHas('likedBy', fn($q) => $q->where('users.id', Auth::id()))
                ->where('user_id', '!=', Auth::id());
        }

        $items = Item::with(['mainImage', 'images', 'purchase'])
            ->latest()
            ->paginate(24);

        $items = $query->paginate(24)->withQueryString();

        return view('items.index', compact('items', 'tab', 'kw'));
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

        if ($with) $item->load($with);
        if ($withCount) $item->loadCount($withCount);

        $liked = false;
        if (Schema::hasTable('likes') && auth()->check()) {
            $liked = $item->relationLoaded('likedBy')
                ? $item->likedBy->contains(auth()->id())
                : $item->likedBy()->whereKey(auth()->id())->exists();
        }

        $isSold = Schema::hasTable('purchases') ? (bool) $item->purchase : false;

        return view('items.show', compact('item', 'liked', 'isSold'));
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
