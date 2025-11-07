@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items-index.css') }}?v=9">
@endpush

@section('title','商品一覧')

@section('content')
@if (session('status'))
<div class="flash flash--success" role="status">
  {{ session('status') }}
</div>
@endif
@php
$tab = $tab ?? (request('tab') === 'mylist' ? 'mylist' : 'recommend');
$q = $q ?? request('q');
@endphp

<nav class="tabs">
  <a href="{{ route('items.index', array_filter(['tab'=>'recommend','q'=>$q])) }}"
    class="tab {{ $tab === 'mylist' ? '' : 'is-active' }}">おすすめ</a>
  <a href="{{ route('items.index', array_filter(['tab'=>'mylist','q'=>$q])) }}"
    class="tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
</nav>

{{-- 未ログインのマイリストは一覧を出さない --}}
@if ($tab === 'mylist' && !auth()->check())

{{-- データが空のとき --}}
@elseif ( (is_object($items) && method_exists($items,'isEmpty') && $items->isEmpty())
|| (is_array($items) && empty($items)) )
<p class="muted">商品がありません。</p>

{{-- 一覧表示 --}}
@else
<ul class="grid {{ $tab === 'mylist' ? 'grid--cap' : '' }}">
  @foreach ($items as $item)
  @php
  // purchase_exists があればそれを、無ければ false にする（stdClass でも安全）
  $isSold = (bool) data_get($item, 'purchase_exists', false);
  @endphp
  <li class="tile {{ $isSold ? 'is-sold' : '' }}">
    @if($isSold)
    {{-- 購入済みはリンク不可＆オーバーレイ表示 --}}
    <div class="tile__link" aria-disabled="true">
      <span class="tile__thumb">
        <img class="tile__img"
          src="{{ $item->image_url ?: asset('images/noimage.svg') }}"
          alt="{{ $item->name }}"
          onerror="this.src='{{ asset('images/noimage.svg') }}'">
        <span class="tile__sold">SOLD</span>
      </span>
      <div class="tile__name">{{ $item->name }}</div>
    </div>
    @else
    {{-- 未購入のみリンク可 --}}
    <a class="tile__link" href="{{ route('items.show', ['item' => $item->id]) }}">
      <span class="tile__thumb">
        <img class="tile__img"
          src="{{ $item->image_url ?: asset('images/noimage.svg') }}"
          alt="{{ $item->name }}"
          onerror="this.src='{{ asset('images/noimage.svg') }}'">
      </span>
      <div class="tile__name">{{ $item->name }}</div>
    </a>
    @endif
  </li>
  @endforeach
</ul>

@if ($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="pagination">
  {{ $items->links() }}
</div>
@endif
@endif
@endsection