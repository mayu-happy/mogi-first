@extends('layouts.app')
@section('title','商品購入')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@php
$src = $item->img_url
? (filter_var($item->img_url, FILTER_VALIDATE_URL) ? $item->img_url : asset($item->img_url))
: asset('images/noimage.png');
@endphp

@section('content')
<div class="checkout">
    <div class="checkout__grid">

        {{-- ===== 左カラム ===== --}}
        <section class="checkout__left">

            {{-- 商品ヘッダー --}}
            <div class="product-head">
                <img class="thumb" src="{{ $src }}" alt="商品画像" loading="lazy">
                <div>
                    <h2 class="name">{{ $item->name }}</h2>
                    <div class="price">¥ {{ number_format($item->price) }}</div>
                </div>
            </div>

            {{-- 支払い方法 --}}
            <div class="panel-rail">
                <h3 class="section-title">支払い方法</h3>
                <form method="POST" action="{{ route('purchase.updatePayment', ['item'=>$item->id]) }}" class="pm-form">
                    @csrf
                    <select name="payment_method" onchange="this.form.submit()">
                        <option value="">選択してください</option>
                        <option value="コンビニ払い" {{ $selectedMethod == 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                        <option value="カード支払い" {{ $selectedMethod == 'カード支払い' ? 'selected' : '' }}>カード支払い</option>
                    </select>
                </form>
            </div>

            {{-- 配送先 --}}
            <div class="panel-rail ship">
                <div class="ship__head">
                    <h3 class="section-title">配送先</h3>
                    <a class="ship__edit" href="{{ route('address.edit', ['back'=>request()->fullUrl(), 'context'=>'purchase']) }}">変更する</a>
                </div>
                <address class="ship__addr">
                    <div>〒 {{ $addr->postal_code }}</div>
                    <div>{{ $addr->address }}</div>
                    @if($addr->building)<div>{{ $addr->building }}</div>@endif
                </address>
            </div>

        </section>

        {{-- ===== 右カラム（サマリー） ===== --}}
        <aside class="checkout__right">
            <div class="summary">
                <div class="summary__row">
                    <div>商品代金</div>
                    <div class="price">¥{{ number_format($item->price) }}</div>
                </div>
                <div class="summary__row">
                    <div>支払い方法</div>
                    <div>
                        {{ $selectedMethod ?: '—' }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('purchase.store', ['item' => $item->id]) }}">
                @csrf
                <input type="hidden" name="payment_method" value="{{ $selectedMethod }}">
                <button type="submit" class="btn-purchase">購入する</button>
            </form>
        </aside>
    </div>
</div>
@endsection