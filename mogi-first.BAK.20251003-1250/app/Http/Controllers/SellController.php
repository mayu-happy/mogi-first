<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SellController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $categories = Category::whereNotNull('name')
            ->where('name', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name']);

        $conditions = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];

        $draft = session('sell.draft', []);

        return view('item.create', compact('categories', 'conditions', 'draft'));
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

        $item->categories()->sync($validated['category_ids']);

        return redirect()->route('mypage.profile')->with('success', '出品が完了しました！');
    }
}
