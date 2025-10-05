@extends('layouts.app')

@section('title', 'マイページ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush

@section('content')
<div class="mypage">

    {{-- ヘッダー（アイコン＋ユーザー名＋編集ボタン） --}}
    <header class="mypage__hero">
        <div class="mypage__avatar">
            @php
            $avatar = $user->avatar_url ?? null;
            $initial = mb_substr($user->name ?? 'U', 0, 1);
            @endphp
            @if($avatar)
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar-lg">
            @else
            <span class="mypage__initial">{{ $initial }}</span>
            @endif
        </div>

        <div class="mypage__name">{{ $user->name ?? 'ユーザー名' }}</div>

        <a href="{{ route('mypage.profile.edit') }}"
            class="mypage__edit-btn">プロフィールを編集</a>
    </header>

    {{-- タブ（出品した商品 / 購入した商品） --}}
    <nav class="mypage__tabs" aria-label="Tabs">
        <a href="{{ route('mypage.sell') }}"
            class="mypage__tab {{ request()->routeIs('mypage.sell') ? 'is-active' : '' }}">
            出品した商品
        </a>
        <a href="{{ route('mypage.buy') }}"
            class="mypage__tab {{ request()->routeIs('mypage.buy') ? 'is-active' : '' }}">
            購入した商品
        </a>
    </nav>

    {{-- グリッド（商品カード） --}}
    @php
    // Controller から $items / $sells / $buys のどれかが来ていればそれを使う
    $items = $items ?? ($sells ?? ($buys ?? []));
    @endphp

    <section class="mypage__grid">
        @forelse($items as $item)
        <a class="card" href="{{ route('items.show', $item) }}">
            <div class="card__img"
                style="background-image:url('{{ $item->img_url ?? asset('images/placeholder.png') }}')"></div>
            <div class="card__name">{{ $item->name }}</div>
        </a>
        @empty
        <p class="mypage__empty">商品がありません</p>
        @endforelse
    </section>

</div>
@endsection