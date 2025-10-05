<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($item_id)
    {
        return view('purchase.index', compact('item_id'));
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:コンビニ払い,カード支払い'],
        ]);

        return view('checkout.confirm', [
            'payment_method' => $validated['payment_method'],
        ]);
    }

    public function create(Item $item, Request $request)
    {
        $methods = ['コンビニ払い', 'カード支払い'];

        if ($request->has('reset')) {
            session()->forget('payment_method_' . $item->id);
        }

        $selectedMethod = session('payment_method_' . $item->id);

        $user = Auth::user();

        $addr = session('checkout.address', [
            'postal_code' => $user->postal_code,
            'address'     => $user->address,
            'building'    => $user->building,
        ]);


        $src = $item->img_url
            ? (filter_var($item->img_url, FILTER_VALIDATE_URL) ? $item->img_url : asset($item->img_url))
            : asset('images/noimage.png');


        return view('purchase.create', [
            'item'            => $item,
            'methods'         => $methods,
            'user'            => $user,
            'addr'            => (object) $addr,
            'selectedMethod'  => $selectedMethod,
            'src'             => $src,
        ]);
    }

    public function history()
    {
        $purchases = Purchase::with('item')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('purchase.history', compact('purchases'));
    }

    public function store(Request $request, Item $item)
    {
        $methods = ['コンビニ払い', 'カード支払い'];

        $data = $request->validate([
            'payment_method' => ['required', Rule::in($methods)],
        ]);

        Purchase::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'payment_method' => $data['payment_method'],
            'price' => $item->price,
            'shipping_postal_code' => $request->user()->postal_code,
            'shipping_address'     => $request->user()->address,
            'shipping_building'    => $request->user()->building,
            'status' => 'paid',
        ]);

        return redirect()->route('mypage.buy')->with('status', '購入が完了しました。');
    }

    public function updatePayment(Request $request, Item $item)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:コンビニ払い,カード支払い',
        ]);

        session(['payment_method_' . $item->id => $validated['payment_method']]);

        $user = $request->user();

        \App\Models\Purchase::firstOrCreate(
            ['user_id' => $user->id, 'item_id' => $item->id],
            [
                'payment_method'       => $validated['payment_method'],
                'price'                => $item->price,
                'shipping_postal_code' => $user->postal_code,
                'shipping_address'     => $user->address,
                'shipping_building'    => $user->building,
                'status'               => 'paid',
            ]
        );

        return redirect()->route('items.index', ['item' => $item->id]);
    }
}
