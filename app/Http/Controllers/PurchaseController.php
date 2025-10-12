<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** 表示用ラベル（★ 2択に限定） */
    private const PAYMENT_LABELS = [
        'conbini' => 'コンビニ支払い',
        'card'    => 'カード支払い',
    ];

    /** 購入確認画面（小計） */
    public function create(Request $request, Item $item)
    {

        if ($item->user_id === auth()->id()) {
            return redirect()->route('items.show', $item)->with('status', '自分の商品は購入できません');
        }
        if ($item->purchase()->exists()) {
            return redirect()->route('items.show', $item)->with('status', 'この商品は売り切れです');
        }

        // 1) クエリ -> セッション -> 既定値 の順で決定
        $paymentKey = $request->query(
            'payment',
            (string) session('purchase.payment', 'conbini')
        );

        // 2) 不正キーは既定値へフォールバック
        if (!array_key_exists($paymentKey, self::PAYMENT_LABELS)) {
            $paymentKey = 'conbini';
        }
        $paymentLabel = self::PAYMENT_LABELS[$paymentKey];

        // 3) 選択肢をセッションに保存（右側の小計表示で使用）
        session(['purchase.payment' => $paymentKey]);

        // 4) 配送先は「購入用セッション」→ なければプロフィールを初期値に
        $user = Auth::user();
        $address = session('purchase.address', [
            'postal_code' => (string) ($user->postal_code ?? 'XXX-YYYY'),
            'address'     => (string) ($user->address ?? 'ここに住所と建物が入ります'),
            'building'    => (string) ($user->building ?? ''),
        ]);

        return view('purchase.create', [
            'item'          => $item,
            'paymentKey'    => $paymentKey,
            'paymentLabel'  => $paymentLabel,
            'paymentLabels' => self::PAYMENT_LABELS,
            'address'       => $address,
        ]);
    }

    /** 支払い方法編集（別ページ方式を使う場合） */
    public function editPayment(Item $item)
    {
        $paymentKey = (string) session('purchase.payment', 'conbini');

        return view('purchase.payment', [
            'item'       => $item,
            'paymentKey' => array_key_exists($paymentKey, self::PAYMENT_LABELS) ? $paymentKey : 'conbini',
            'labels'     => self::PAYMENT_LABELS,
        ]);
    }

    /** 支払い方法の保存 → 確認画面へ */
    public function updatePayment(Request $request, Item $item)
    {
        $data = $request->validate([
            'payment' => ['required', 'in:conbini,card'], // ★ 2択のみ許可
        ]);

        session(['purchase.payment' => $data['payment']]);

        // 再送信ダイアログ回避
        return redirect()->route('purchase.create', $item)->setStatusCode(303);
    }

    /** 購入用：配送先の編集画面 */
    public function editAddress(Item $item, Request $request)
    {
        $user = Auth::user();
        $address = $request->session()->get('purchase.address', [
            'postal_code' => (string) ($user->postal_code ?? ''),
            'address'     => (string) ($user->address ?? ''),
            'building'    => (string) ($user->building ?? ''),
        ]);

        return view('purchase.address', compact('item', 'address'));
    }

    /** 購入用：配送先の更新（セッション保存のみ・プロフィールは触らない） */
    public function updateAddress(Item $item, Request $request)
    {
        $data = $request->validate([
            'postal_code' => ['required', 'regex:/^\d{3}-?\d{4}$/'],
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
        ]);

        // 1234567 を 123-4567 へ補正
        $postal = str_replace('-', '', $data['postal_code']);
        $postal = preg_replace('/^(\d{3})(\d{4})$/', '$1-$2', $postal);

        $request->session()->put('purchase.address', [
            'postal_code' => $postal,
            'address'     => $data['address'],
            'building'    => $data['building'] ?? '',
        ]);

        return redirect()->route('purchase.create', $item)->with('status', '配送先を更新しました');
    }

    /** 購入確定 */
    public function store(Request $request, Item $item)
    {

        if ($item->user_id === auth()->id() || $item->purchase()->exists()) {
            return redirect()->route('items.show', $item)->with('status', '購入できません');
        }
        
        $user       = Auth::user();
        $paymentKey = (string) session('purchase.payment', 'conbini');

        // 念のため最終バリデーション（2択のみ）
        if (!array_key_exists($paymentKey, self::PAYMENT_LABELS)) {
            $paymentKey = 'conbini';
        }

        // 配送先はセッションの値（プロフィールは変更しない）
        $addr = session('purchase.address', [
            'postal_code' => (string) ($user->postal_code ?? ''),
            'address'     => (string) ($user->address ?? ''),
            'building'    => (string) ($user->building ?? ''),
        ]);

        // 在庫チェックなどをここで実施（必要なら）

        Purchase::create([
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'payment_method' => $paymentKey,          // 内部値はキーで保存
            'postal_code'    => $addr['postal_code'] ?? '',
            'address'        => $addr['address'] ?? '',
            'building'       => $addr['building'] ?? '',
        ]);

        // セッションクリア
        Session::forget(['purchase.payment', 'purchase.address']);

        return redirect()->route('mypage.buy')->with('success', '購入が完了しました。');
    }
}
