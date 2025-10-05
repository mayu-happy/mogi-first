@extends('layouts.base')

@section('content')
@php
// タブ（?tab=mylist ならマイリスト、それ以外はおすすめ）
$tab = request('tab', 'recommend');
@endphp

{{-- タブ --}}
<nav class="tabs">
    <a href="{{ route('items.index', ['tab' => 'recommend']) }}"
        class="tab {{ $tab === 'recommend' ? 'is-active' : '' }}">おすすめ</a>
    <a href="{{ route('items.index', ['tab' => 'mylist']) }}"
        class="tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
</nav>

{{-- 枠なしグリッド：画像＋商品名のみ（※金額は出さない） --}}
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
            {{-- ★ 価格は表示しない：以下を出していたら削除
            <div class="tile__price">¥{{ number_format($it->price) }}</div>
            --}}
        </a>
    </li>
    @endforeach
</ul>
<div class="pagination">{{ $items->links() }}</div>
@endif
@endsection