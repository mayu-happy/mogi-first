<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ① 初回設定（空で表示）
    public function create()
    {
        $user = Auth::user();
        $mode = 'create';
        return view('mypage.profile_edit', compact('user', 'mode'));
    }

    // ① 初回保存
    public function store(Request $request)
    {
        $user  = Auth::user();
        $data  = $this->validated($request);

        // 画像
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('profile_images', 'public');
        }

        $user->fill($data)->save();

        // 初回はマイページのプロフィール表示へ
        return redirect()->route('home')->with('status', 'プロフィールを設定しました');
    }

    // ② 編集（既存値で表示）
    public function edit()
    {
        $user = Auth::user();
        $mode = 'edit';
        return view('mypage.profile_edit', compact('user', 'mode'));
    }

    // ② 更新

    public function update(Request $request)
    {
        // create/store と共通のバリデーションルールを利用
        $data = $this->validated($request);

        // zipcode → postal_code の補完を使わないなら、この if は消してOK
        if (!empty($data['zipcode']) && empty($data['postal_code'])) {
            $zip = preg_replace('/\D/', '', $data['zipcode']);
            if (preg_match('/^\d{7}$/', $zip)) {
                $data['postal_code'] = substr($zip, 0, 3) . '-' . substr($zip, 3);
            }
        }
        unset($data['zipcode']);

        // プロフィールフォームで画像を扱うとき用（今は特に何も来なければスルー）
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('profile_images', 'public');
        }

        // ユーザー情報更新
        $request->user()->fill($data)->save();

        // ★ここで必ずホーム画面へ戻す
        return redirect()->route('home')
            ->with('status', 'プロフィールを更新しました');
    }

    // アバターだけ更新（別フォーム用）
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // 既存画像があれば削除（public ディスク）
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        $path = $request->file('image')->store('profile_images', 'public');
        $user->image = $path;
        $user->save();

        return redirect()->route('home')->with('status', 'アイコンを更新しました');
    }

    // ===== 共通バリデーション =====
    private function validated(Request $request): array
    {
        return $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'zipcode'      => ['nullable', 'string', 'max:10'],              // 互換入力
            // 'postal_code'  => ['nullable', 'regex:/^\d{3}-?\d{4}$/'],         // 123-4567 or 1234567
            'postal_code'  => ['nullable', 'string', 'max:20'], // ←いったんゆるく  
            'address'      => ['nullable', 'string', 'max:255'],
            'building'     => ['nullable', 'string', 'max:255'],
            'image'        => ['nullable', 'image', 'max:5120'],             // 5MB
        ], [
            'name.required'       => 'ユーザー名を入力してください。',
            'postal_code.regex'   => '郵便番号は 123-4567 形式で入力してください。',
            'image.image'         => '画像ファイルを選択してください。',
            'image.max'           => '画像サイズは5MB以下にしてください。',
        ]);
    }
}
