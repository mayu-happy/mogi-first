@extends('layouts.app')
@section('title','購入手続き')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}?v=3">
@endpush

@section('content')
@php
// コントローラから来る値
// $paymentLabel ... 例「コンビニ支払い」
// $paymentKey ... 例「conbini」
// $address ... [postal_code, address, building]
// $item ... 対象商品

$addr = is_array($address ?? null) ? $address : [];
$pc = $addr['postal_code'] ?? 'XXX-YYYY';
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

    {{-- 支払い方法 --}}
    <div class="line"></div>
    <div>
      <div class="h">支払い方法</div>

      <div class="payment-apply">
        <select
          id="paymentSelect"
          name="payment"
          class="select">

          <option value="">選択してください</option>
          <option value="conbini" {{ ($paymentKey ?? '')==='conbini' ? 'selected' : '' }}>コンビニ支払い</option>
          <option value="card" {{ ($paymentKey ?? '')==='card'    ? 'selected' : '' }}>カード支払い</option>
        </select>
      </div>
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
        <div class="summary__td" id="summaryPayment">
          {{ $paymentLabel ?? '―' }}
        </div>
      </div>
    </aside>

    {{-- 最終送信フォーム（購入する） --}}
    <form method="POST" action="{{ route('purchase.store', $item) }}" class="summary-buy" id="purchaseForm">
      @csrf
      {{-- 選択中の支払い方法を送る hidden --}}
      <input type="hidden" name="payment" id="paymentHidden" value="{{ $paymentKey ?? '' }}">
      <button type="submit" class="btn-primary">購入する</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function() {
    // セレクト、サマリー表示欄、hiddenフィールドを取得
    const selectEl = document.getElementById('paymentSelect');
    const summaryEl = document.getElementById('summaryPayment');
    const hiddenEl = document.getElementById('paymentHidden');

    if (!selectEl || !summaryEl || !hiddenEl) return;

    // value -> 表示用ラベル の対応マップ
    const labelMap = {
      "": "―",
      "conbini": "コンビニ支払い",
      "card": "カード支払い"
    };

    // 現在のセレクト状態を右側とhiddenに反映
    function syncPaymentDisplay() {
      const val = selectEl.value;
      summaryEl.textContent = labelMap[val] ?? "―";
      hiddenEl.value = val;
    }

    // 初期表示時に同期
    syncPaymentDisplay();

    // ユーザーが支払い方法を変更したら即同期
    selectEl.addEventListener('change', syncPaymentDisplay);
  })();
</script>
@endpush