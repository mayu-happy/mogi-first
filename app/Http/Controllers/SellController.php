<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\ItemImage;

class SellController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $order = [
            'ファッション',
            '家電',
            'インテリア',
            'レディース',
            'メンズ',
            'コスメ',
            '本',
            'ゲーム',
            'スポーツ',
            'キッチン',
            'ハンドメイド',
            'アクセサリー',
            'おもちゃ',
            'ベビー・キッズ',
        ];

        $categories = \App\Models\Category::whereNotNull('name')
            ->where('name', '!=', '')
            ->get(['id', 'name'])
            ->sortBy(fn($c) => (($p = array_search($c->name, $order, true)) === false) ? 999 : $p)
            ->values();

        $conditions = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];
        $draft = session('sell.draft', []);

        $draftImages = session('draft_images', []); // ['public/items/xxx.jpg', ...]
        return view('sell.create', compact('categories', 'conditions', 'draftImages'));
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
            'price'       => ['required', 'integer', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'condition'   => ['required', 'string'],
            'brand'       => ['nullable', 'string', 'max:50'],
            'image'       => ['nullable', 'image', 'max:5120'],
        ]);

        $tmpPath = null;
        $tmpUrl  = null;
        if ($request->hasFile('image')) {
            $tmpPath = $request->file('image')->store('tmp/items', 'public');
            $tmpUrl  = Storage::url($tmpPath);
        }

        $draft = array_merge($data, [
            'tmp_image' => $tmpPath,
        ]);
        session(['sell.draft' => $draft]);

        return view('item.preview', [
            'data'    => $draft,
            'tmpUrl'  => $tmpUrl,
            'category' => Category::find($data['category_id']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'price'       => ['required', 'integer', 'min:1'],
            'condition'   => ['nullable', 'string'],
            'brand'       => ['nullable', 'string', 'max:255'],
            'image'       => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'category_ids'   => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]);

        $imgUrl = null;
        if ($request->hasFile('image')) {
            $path   = $request->file('image')->store('items', 'public');
            $imgUrl = Storage::url($path);
        }

        $item = Item::create([
            'user_id'     => $request->user()->id,
            'name'        => $validated['name'],
            'description' => $validated['description'],
            'price'       => $validated['price'],
            'condition'   => $validated['condition'],
            'brand'       => $validated['brand'] ?? null,
            'img_url'     => $imgUrl,
        ]);

        foreach (session('draft_images', []) as $path) {
            ItemImage::create(['item_id' => $item->id, 'path' => $path]);
        }

        $request->session()->forget('draft_images');

        $item->categories()->sync($validated['category_ids']);

        return redirect()->route('sell.create')->with('status', '出品しました');
    }

    public function uploadImages(Request $request)
    {
        $request->validate([
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $draft =  [];

        foreach ($request->file('images', []) as $file) {
            $path     = $file->store('public/items');  // storage/app/public/items
            $draft[]  = $path;                         // セッションへ積む
        }

        $request->session()->put('draft_images', array_values(array_unique($draft)));

        // ★ ここを back() ではなく create に固定で戻す
        return redirect()->route('sell.create')
            ->with('image_status', '画像をアップロードしました');
    }
}
