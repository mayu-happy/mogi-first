@extends('layouts.app')
@section('title','購入手続き')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}?v=1">
@endpush

@section('content')
<div class="wrap">

    {{-- 左：内容 --}}
    <div>

        {{-- 商品行（罫線で区切り） --}}
        <div class="row">
            <img class="thumb"
                src="{{ $item->img_url && filter_var($item->img_url, FILTER_VALIDATE_URL) ? $item->img_url : asset('images/noimage.png') }}"
                alt="{{ $item->name }}">
            <div>
                <div class="h">{{ $item->name }}</div>
                <div class="price">¥{{ number_format($item->price) }}</div>
            </div>
        </div>


        {{-- 支払い方法（セレクト + 反映ボタン極小） --}}
        <div class="line"></div>
        <div>
            <div class="h">支払い方法</div>

            <form method="GET" action="{{ route('purchase.create', $item) }}">
                <div class="payment-apply">
                    <select name="payment" class="select">
                        <option value="">選択してください</option>
                        <option value="conbini" {{ $paymentKey==='conbini' ? 'selected' : '' }}>コンビニ支払い</option>
                        <option value="card" {{ $paymentKey==='card'    ? 'selected' : '' }}>カード支払い</option>
                    </select>

                    {{-- 右下にぴったり配置する極小ボタン --}}
                    <button type="submit" class="btn-apply" aria-label="反映する">反映する</button>
                </div>
            </form>
        </div>

        <div class="line"></div>

        {{-- 配送先 --}}
        <div>
            <div class="h">配送先</div>
            <div style="line-height:1.8;">
                〒 {{ $address['postal_code'] ?? 'XXX-YYYY' }}<br>
                {{ $address['address'] ?? 'ここに住所と建物が入ります' }}<br>
                {{ $address['building'] ?? '' }}
                <a href="{{ route('purchase.address.edit', $item) }}" style="margin-left:12px;font-size:12px;">変更する</a>
            </div>
        </div>

    </div>

    {{-- 右カラム：カード + ボタン --}}
    <div class="summary-col">
        <aside class="summary">
            <div class="summary__row">
                <div class="summary__th">商品代金</div>
                <div class="summary__td">¥{{ number_format($item->price) }}</div>
            </div>
            <div class="summary__row">
                <div class="summary__th">支払い方法</div>
                <div class="summary__td">
                    @php $labels=['conbini'=>'コンビニ支払い','card'=>'カード支払い']; @endphp
                    {{ $paymentKey ? ($labels[$paymentKey] ?? '―') : '―' }}
                </div>
            </div>
        </aside>

        <form method="POST" action="{{ route('purchase.store', $item) }}" class="summary-buy">
            @csrf
            <input type="hidden" name="payment" value="{{ $paymentKey }}">
            <button type="submit" class="btn-primary">購入する</button>
        </form>
    </div>
</div>@endsection