{{-- resources/views/items/index.blade.php --}}
@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items-index.css') }}?v=4"> {{-- ← キャッシュ回避のために ?v を更新 --}}
@endpush

@section('title','商品一覧')

@section('content')
@php $tab = request('tab','recommend'); $q = request('q'); @endphp

<nav class="tabs">
  <a href="{{ route('items.index', array_filter(['tab'=>'recommend','q'=>$q])) }}"
    class="tab {{ $tab === 'mylist' ? '' : 'is-active' }}">おすすめ</a>
  <a href="{{ route('items.index', array_filter(['tab'=>'mylist','q'=>$q])) }}"
    class="tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
</nav>

@if($items->isEmpty())
<p class="muted">商品がありません。</p>
@else
<ul class="grid">
  @foreach($items as $it)
  @php $sold = (bool) $it->purchase_exists; @endphp
  <li class="tile">
    <a class="tile__link"
      href="{{ $sold ? 'javascript:void(0);' : route('items.show',$it) }}"
      @if($sold) aria-disabled="true" aria-label="売り切れのため購入不可" @endif>

      {{-- 画像用ラッパー（幅100%） --}}
      <span class="tile__thumb">
        <img class="tile__img"
          src="{{ $it->img_url ?? asset('images/noimage.png') }}"
          alt="{{ $it->name }}">
        @if($sold)
        <span class="sold-badge">SOLD</span>
        @endif
      </span>

      <div class="tile__name">{{ $it->name }}</div>
    </a>
  </li>
  @endforeach
</ul>
<div class="pagination">{{ $items->links() }}</div>
@endif
@endsection