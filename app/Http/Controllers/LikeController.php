<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle(Request $request, Item $item)
    {
        $request->user()->likes()->toggle($item->id);

        return $this->redirectAfter($request, $item);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'redirect_to' => ['nullable', 'string'],
        ]);

        $item = Item::findOrFail($request->input('item_id'));

        $request->user()->likes()->syncWithoutDetaching([$item->id]);

        return $this->redirectAfter($request, $item);
    }

    public function destroy(Item $item, Request $request)
    {
        $request->user()->likes()->detach($item->id);

        return $this->redirectAfter($request, $item);
    }

    private function redirectAfter(Request $request, Item $item)
    {
        $to = $request->input('redirect_to');

        if ($to === 'detail') {
            return redirect()->route('items.show', ['item' => $item->id]);
        }

        if ($to === 'home-mylist') {
            return redirect()->route('home', ['tab' => 'mylist']);
        }

        return redirect()->back();
    }
}
