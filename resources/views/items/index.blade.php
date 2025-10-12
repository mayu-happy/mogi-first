@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items-index.css') }}?v=8">
@endpush

@section('title','商品一覧')

@section('content')
@php
$tab = request('tab', 'recommend');
$q = request('q');
@endphp

<nav class="tabs">
  <a href="{{ route('items.index', array_filter(['tab'=>'recommend','q'=>$q])) }}"
    class="tab {{ $tab === 'mylist' ? '' : 'is-active' }}">おすすめ</a>
  <a href="{{ route('items.index', array_filter(['tab'=>'mylist','q'=>$q])) }}"
    class="tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
</nav>

@if($items->isEmpty())
<p class="muted">商品がありません。</p>
@else
<ul class="grid {{ $tab === 'mylist' ? 'grid--cap' : '' }}">
  @foreach($items as $item)
  <li class="tile">
    <a class="tile__link" href="{{ route('items.show', $item) }}">
      <span class="tile__thumb">
        <img
          class="tile__img"
          src="{{ $item->image_url ?: asset('images/noimage.svg') }}"
          alt="{{ $item->name }}"
          loading="eager"
          onerror="this.src='{{ asset('images/noimage.svg') }}'">
      </span>
      <div class="tile__name">{{ $item->name }}</div>
    </a>
  </li>
  @endforeach
</ul>
<div class="pagination">
  {{ $items->links() }}
</div>
@endif
@endsection