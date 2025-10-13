<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\ItemImage;
use Illuminate\Support\Facades\Storage;

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
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price'       => ['required', 'integer', 'min:1'],
            'condition'   => ['required', 'string', 'max:50'],
            'brand'       => ['nullable', 'string', 'max:255'],
            'image'       => ['required', 'image', 'max:5120'],
            'categories'  => ['array'], // 複数カテゴリ対応なら
            'categories.*' => ['integer', 'exists:categories,id'],
        ]);

        // 画像保存
        $relPath = $request->file('image')->store('items', 'public'); // => items/xxxx.jpg

        // アイテム作成
        $item = \App\Models\Item::create([
            'user_id'     => auth()->id(),
            'name'        => $data['name'],
            'description' => $data['description'],
            'price'       => $data['price'],
            'condition'   => $data['condition'],
            'brand'       => $data['brand'] ?? null,
            'img_url'     => $relPath, // ★ 画像パスを items.img_url に
        ]);

        // 中間テーブルにカテゴリを付与（必要な場合）
        if (!empty($data['categories'])) {
            $item->categories()->sync($data['categories']);
        }

        // サブ画像テーブルを使う場合はメイン画像として 1 レコード作っておく（オプション）
        if (method_exists($item, 'images')) {
            $item->images()->create([
                'path' => $relPath,
                'is_main' => false,
            ]);
        }

        return redirect()->route('items.show', $item)->with('status', '出品が完了しました');
    }

    public function uploadImages(Request $request)
    {
        $request->validate([
            'images'   => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ]);

        $draft = collect($request->session()->get('draft_images', []));

        foreach ($request->file('images', []) as $file) {
            $path = $file->store('items', 'public');
            $draft->push($path);
        }

        $request->session()->put('draft_images', $draft->unique()->values()->all());

        return redirect()
            ->route('sell.create')
            ->with('image_status', '画像をアップロードしました');
    }
}
