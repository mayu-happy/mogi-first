{{-- resources/views/purchase/payment.blade.php --}}
@extends('layouts.app')
@section('title','支払い方法を選択')

@section('content')
<div class="container" style="max-width:720px;margin:0 auto;">
    <h1 style="text-align:center;">支払い方法</h1>

    {{-- ★この2文言を表示することがテスト要件 --}}
    <form method="POST" action="{{ route('purchase.payment.update', $item) }}" style="display:grid;gap:12px;">
        @csrf
        @method('PUT')
        <label>支払い方法
            <select name="payment">
                <option value="conbini" {{ ($paymentKey ?? '')==='conbini' ? 'selected' : '' }}>コンビニ支払い</option>
                <option value="card" {{ ($paymentKey ?? '')==='card'    ? 'selected' : '' }}>カード支払い</option>
            </select>
        </label>

        <div style="text-align:right;margin-top:8px;">
            <button type="submit">反映する</button>
        </div>
    </form>
</div>
@endsection