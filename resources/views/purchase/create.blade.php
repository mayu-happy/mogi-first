@extends('layouts.app')
@section('title','購入手続き')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}?v=3">
@endpush

@section('content')
@php
    // コントローラから来る値だけを使う（$paymentLabel, $paymentKey, $address, $item）
    $addr = is_array($address ?? null) ? $address : [];
    $pc  = $addr['postal_code'] ?? 'XXX-YYYY';
    $adr = $addr['address'] ?? 'ここに住所と建物が入ります';
    $bld = $addr['building'] ?? '';
@endphp

<div class="wrap">

  {{-- 左：内容 --}}
  <div>

    {{-- 商品行 --}}
    <div class="row">
      <img
        src="{{ $item->image_url ?: asset('images/noimage.svg') }}"
        alt="{{ $item->name }}"
        width="160" height="160"
        loading="eager"
        onerror="this.onerror=null;this.src='{{ asset('images/noimage.svg') }}';">
      <div>
        <div class="h">{{ $item->name }}</div>
        <div class="price">¥{{ number_format($item->price) }}</div>
      </div>
    </div>

    {{-- 支払い方法（セレクト + 反映） --}}
    <div class="line"></div>
    <div>
      <div class="h">支払い方法</div>

      {{-- ★ テストでも使いやすい PUT 方式（セッションに保存 → 自ページへリダイレクト） --}}
      <form method="POST" action="{{ route('purchase.payment.update', $item) }}">
        @csrf
        @method('PUT')
        <div class="payment-apply">
          <select name="payment" class="select">
            <option value="">選択してください</option>
            <option value="conbini" {{ ($paymentKey ?? '')==='conbini' ? 'selected' : '' }}>コンビニ支払い</option>
            <option value="card"    {{ ($paymentKey ?? '')==='card'    ? 'selected' : '' }}>カード支払い</option>
          </select>
          <button type="submit" class="btn-apply" aria-label="反映する">反映する</button>
        </div>
      </form>
    </div>

    <div class="line"></div>

    {{-- 配送先 --}}
    <div>
      <div class="h">配送先</div>
      <div style="line-height:1.8;">
        〒 {{ $pc }}<br>
        {{ $adr }}<br>
        {{ $bld }}
        <a href="{{ route('purchase.address.edit', $item) }}" style="margin-left:12px;font-size:12px;">変更する</a>
      </div>
    </div>

  </div>

  {{-- 右：小計 + 購入 --}}
  <div class="summary-col">
    <aside class="summary">
      <div class="summary__row">
        <div class="summary__th">商品代金</div>
        <div class="summary__td">¥{{ number_format($item->price) }}</div>
      </div>
      <div class="summary__row">
        <div class="summary__th">支払い方法</div>
        <div class="summary__td">
          {{-- コントローラが渡すラベルをそのまま表示（null ならダッシュ） --}}
          {{ $paymentLabel ?? '―' }}
        </div>
      </div>
    </aside>

    <form method="POST" action="{{ route('purchase.store', $item) }}" class="summary-buy">
      @csrf
      <input type="hidden" name="payment" value="{{ $paymentKey ?? '' }}">
      <button type="submit" class="btn-primary">購入する</button>
    </form>
  </div>
</div>
@endsection
