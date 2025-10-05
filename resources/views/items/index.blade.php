@extends('layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/items-index.css') }}?v=2">
@endpush


@section('content')
@php
// ?tab=mylist ならマイリスト、それ以外はおすすめ
$tab = request('tab', 'recommend');
@endphp

{{-- タブ --}}

@php $q = request('q'); @endphp
<nav class="tabs" style="display:flex;gap:16px;margin-bottom:16px;">
  <a href="{{ route('items.index', array_filter(['tab'=>'recommend','q'=>$q])) }}"
    class="tab {{ $tab === 'mylist' ? '' : 'is-active' }}">おすすめ</a>

  <a href="{{ route('items.index', array_filter(['tab'=>'mylist','q'=>$q])) }}"
    class="tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
</nav>

{{-- 枠なしグリッド：画像＋商品名のみ（価格は出さない） --}}
@if($items->isEmpty())
<p class="muted">商品がありません。</p>
@else
<ul class="grid grid--plain">
  @foreach($items as $it)
  <li class="tile">
    <a class="tile__link" href="{{ route('items.show', $it) }}">
      <img class="tile__img"
        src="{{ $it->img_url ?? asset('images/noimage.png') }}"
        alt="{{ $it->name }}">
      <div class="tile__name">{{ $it->name }}</div>
      {{-- 価格は表示しない（安全のため残骸があっても消す） --}}
      {{-- <div class="tile__price">¥{{ number_format($it->price) }}</div> --}}
    </a>
  </li>
  @endforeach
</ul>
<div class="pagination">{{ $items->links() }}</div>
@endif
@endsection