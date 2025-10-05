<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class MyPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * マイページ（プロフィール＋タブ切替：sell / buy / likes）
     * /mypage や /mypage/profile からここに来る想定
     */
    public function profile(Request $request)
    {
        $user = Auth::user()->loadCount(['items', 'purchases', 'favorites']);

        // タブと検索語（スペース区切り）を取得
        $tab   = $request->query('tab', 'sell');                 // sell|buy|likes
        $q     = trim((string) $request->query('q', ''));
        $terms = $q !== '' ? preg_split('/[\s　]+/u', $q, -1, PREG_SPLIT_NO_EMPTY) : [];

        // タブごとにベースクエリ
        if ($tab === 'buy') {
            $query = Item::with('purchase')
                ->whereHas('purchase', fn($qq) => $qq->where('user_id', $user->id));
        } elseif ($tab === 'likes') {
            // User::likedItems() が必要（下にリレーション記述あり）
            $query = $user->likedItems()->with('purchase');
        } else {
            $tab = 'sell';
            $query = $user->items()->with('purchase');
        }

        // キーワード（AND）検索: name LIKE %term%
        foreach ($terms as $t) {
            $query->where('name', 'like', '%' . $t . '%');
        }

        $items = $query->latest()->paginate(12)->withQueryString();

        return view('mypage.profile', [
            'user'  => $user,
            'tab'   => $tab,
            'items' => $items,
            'q'     => $q,
        ]);
    }

    /**
     * 既存リンク互換: /mypage/sell, /mypage/buy, /mypage/likes は
     * すべて profile に寄せる（URLはそのままでOK）
     */
    public function sell()
    {
        return redirect()->route('mypage.profile', ['tab' => 'sell']);
    }

    public function buy()
    {
        return redirect()->route('mypage.profile', ['tab' => 'buy']);
    }

    public function likes()
    {
        return redirect()->route('mypage.profile', ['tab' => 'likes']);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address'     => ['nullable', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'image'       => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
        ]);

        $user = auth()->user();

        if ($request->hasFile('image')) {
            // 旧画像を消す（あれば）
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            // 新画像を保存
            $data['image'] = $request->file('image')->store('profile_images', 'public');
        }

        $user->update($data);

        return redirect()->route('mypage.profile')->with('success', 'プロフィールを更新しました');
    }
}
