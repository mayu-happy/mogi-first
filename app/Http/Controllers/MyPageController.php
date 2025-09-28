<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class MyPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function sell(Request $request)
    {
        $user = auth()->user();
        $q = trim((string) $request->query('q', ''));
        $terms = $q !== '' ? preg_split('/[\s　]+/u', $q, -1, PREG_SPLIT_NO_EMPTY) : [];

        $query = $user->items()->with('purchase');

        foreach ($terms as $t) {
            $query->where('name', 'like', '%' . $t . '%');
        }

        $items = $query->latest()->paginate(24)->withQueryString();
        return view('mypage.index', [
            'user'  => $user,
            'items' => $items,
            'tab'   => 'sell',
            'q'     => $q,
        ]);
    }

    public function buy(Request $request)
    {
        $user = auth()->user();
        $q = trim((string) $request->query('q', ''));
        $terms = $q !== '' ? preg_split('/[\s　]+/u', $q, -1, PREG_SPLIT_NO_EMPTY) : [];

        $query = Item::with('purchase')
            ->whereHas('purchase', fn($qq) => $qq->where('user_id', $user->id));

        foreach ($terms as $t) {
            $query->where('name', 'like', '%' . $t . '%');
        }

        $items = $query->latest()->paginate(24)->withQueryString();

        return view('mypage.index', [
            'user'  => $user,
            'items' => $items,
            'tab'   => 'buy',
            'q'     => $q,
        ]);
    }

    public function likes()
    {
        $user = auth()->user();

        $items = $user->likedItems()
            ->with('purchases')                 // SOLD 表示用
            ->orderBy('likes.created_at', 'desc')
            ->paginate(12);

        $keyword = session('search.q');


        return view('mypage.likes', [
            'items' => $items,
            'keyword' => $keyword,
        ]);
    }

    public function profile()
    {
        $user = Auth::user()->load(['profile', 'items', 'purchases.item']);
        $sells = $user->items;
        $buys  = $user->purchases->pluck('item')->filter();
        return view('mypage.profile', compact('user', 'sells', 'buys'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('mypage.profile.edit', compact('user'));
    }
    
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address'     => ['nullable', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'image'       => ['nullable', 'image'],
        ]);

        $user = Auth::user();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile_images', 'public');
            $data['image'] = $path; // 例: profile_images/a.png
        }

        $user->update($data);

        return redirect()->route('mypage.profile');
    }
}
