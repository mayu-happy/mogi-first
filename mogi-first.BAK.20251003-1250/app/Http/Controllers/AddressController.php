<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 住所変更フォーム表示
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $context = $request->query('context');
        if ($context === 'purchase') {
            $addr = session('checkout.address', [
                'postal_code' => $user->postal_code,
                'address'     => $user->address,
                'building'    => $user->building,
            ]);
        } else {
            $addr = [
                'postal_code' => $user->postal_code,
                'address'     => $user->address,
                'building'    => $user->building,
            ];
        }

        return view('purchase.address_edit', [
            'addr'    => (object) $addr,
            'back'    => $request->query('back', url()->previous()),
            'context' => $context,
        ]);
    }

    /**
     * 更新処理
     */
    public function update(Request $request)
    {
        $context = $request->input('context');
        $data = $request->validate([
            'postal_code' => ['required', 'string', 'max:20'],
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'back'        => ['nullable', 'url'],
            'context'     => ['nullable', 'in:purchase'],
        ]);

        if ($context === 'purchase') {
            session(['checkout.address' => [
                'postal_code' => $data['postal_code'],
                'address'     => $data['address'],
                'building'    => $data['building'] ?? null,
            ]]);

            return redirect($data['back'] ?? route('mypage.address'))
                ->with('message', '配送先を更新しました（今回のみ）');
        }
        $user = $request->user();
        $user->postal_code = $data['postal_code'];
        $user->address     = $data['address'];
        $user->building    = $data['building'] ?? null;
        $user->save();

        return back()->with('message', 'プロフィール住所を更新しました');
    }
}
