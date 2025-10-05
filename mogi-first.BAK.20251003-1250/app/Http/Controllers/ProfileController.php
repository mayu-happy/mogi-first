<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'zipcode'     => ['nullable', 'string', 'max:10'],           // 使う方だけ残せばOK
            'postal_code' => ['nullable', 'regex:/^\d{3}-?\d{4}$/'],    // テストに合わせてどちらかに統一しても良い
            'address'     => ['nullable', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'image'       => ['nullable', 'image', 'max:5120'],
        ], [
            'postal_code.regex' => '郵便番号は 123-4567 形式で入力してください。',
        ]);

        if ($request->hasFile('image')) {
            // ✅ publicディスクに保存（profile_images/ハッシュ名）
            $path = $request->file('image')->store('profile_images', 'public');
            $data['image'] = $path; // 例: "profile_images/abcd1234.png"
        }

        $user->fill($data)->save();

        $back = $request->input('return');
        $canBack = $back && Str::startsWith($back, url('/'));

        return $canBack ? redirect()->to($back) : redirect()->route('mypage.profile');
    }

    public function show()
    {
        $user = auth()->user()->load([
            'items',
            'purchases.item',
        ]);

        $sells = $user->items;
        $buys  = $user->purchases->pluck('item')->filter();

        return view('mypage.profile', compact('user', 'sells', 'buys'));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        if ($request->hasFile('image')) {
            // ✅ publicディスクを明示して、保存先は profile_images/
            $path = $request->file('image')->store('profile_images', 'public');
            $data['image'] = $path; // 例) "profile_images/xxxx.png"
        }

        return redirect()->back();
    }
}
