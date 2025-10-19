@extends('layouts.app')

@section('title', 'マイページ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items-index.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush

@section('content')
<div class="mypage">

    {{-- ヘッダー --}}
    <header class="mypage__hero">
        <div class="mypage__avatar">
            {{-- 常にアバター表示（プレースホルダー含む） --}}
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar-lg">
        </div>
        <div class="mypage__name">{{ $user->name ?? 'ユーザー名' }}</div>
        <a href="{{ route('mypage.profile.edit') }}" class="mypage__edit-btn">プロフィールを編集</a>
    </header>

    {{-- タブ --}}
    @php
    $tab = $tab ?? request('tab','sell');
    $isSellTab = $tab === 'sell';
    $isBuyTab = $tab === 'buy';
    @endphp
    <nav class="mypage__tabs" aria-label="Tabs">
        <a href="{{ route('mypage.sell') }}"
            class="mypage__tab {{ $isSellTab ? 'is-active' : '' }}">
            出品した商品
        </a>
        <a href="{{ route('mypage.buy') }}"
            class="mypage__tab {{ $isBuyTab ? 'is-active' : '' }}"
            aria-label="購入したアイテム">
            購入した商品
        </a>
    </nav>

    @php
    // 一覧データ（基本は $items が来る）
    $itemsVar = $items ?? ($sells ?? ($buys ?? collect()));
    if (is_array($itemsVar)) { $itemsVar = collect($itemsVar); }
    @endphp

    <section class="mypage__list">
        @if((is_object($itemsVar) && method_exists($itemsVar,'count') && $itemsVar->count() === 0)
        || (is_array($itemsVar) && count($itemsVar) === 0))
        <p class="mypage__empty">商品がありません</p>
        @else
        <ul class="grid">
            @foreach($itemsVar as $item)
            @php
            $src = $item->image_url ?: asset('images/noimage.svg');
            $isMine = auth()->check() && $item->user_id === auth()->id();

            // 売り切れ判定（リレーションが読まれていれば relationLoaded 経由で軽量）
            $isSold = $item->relationLoaded('purchase')
            ? (bool) $item->purchase
            : $item->purchase()->exists();

            // クリック不可条件：
            // 1) 自分の出品 2) 売り切れ 3) 「購入した商品」タブ内
            $disabled = $isMine || $isSold || $isBuyTab;

            // バッジ文言（中央SOLD/購入済みを想定）
            $badge = $isBuyTab ? '購入済み' : ($isSold ? 'SOLD' : ($isMine ? 'あなたの出品' : null));
            @endphp

            <li class="tile {{ $disabled ? 'is-disabled' : '' }}">
                @if($disabled)
                {{-- 非活性（クリック不可） --}}
                <div class="tile__link" aria-disabled="true">
                    <span class="tile__thumb">
                        @if($badge)<span class="sold-overlay">{{ $badge }}</span>@endif
                        <img class="tile__img"
                            src="{{ $src }}"
                            alt="{{ $item->name }}"
                            onerror="this.src='{{ asset('images/noimage.svg') }}'">
                    </span>
                    <div class="tile__name">{{ $item->name }}</div>
                </div>
                @else
                {{-- 通常リンク --}}
                <a href="{{ route('items.show', $item) }}" class="tile__link">
                    <span class="tile__thumb">
                        <img class="tile__img"
                            src="{{ $src }}"
                            alt="{{ $item->name }}"
                            onerror="this.src='{{ asset('images/noimage.svg') }}'">
                    </span>
                    <div class="tile__name">{{ $item->name }}</div>
                </a>
                @endif
            </li>
            @endforeach
        </ul>

        {{-- ページネーション（LengthAwarePaginator のときだけ表示） --}}
        @if($itemsVar instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mypage__pager">
            {{ $itemsVar->withQueryString()->links() }}
        </div>
        @endif
        @endif
    </section>

</div>
@endsection