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
            @php
            $avatar = $user->avatar_url ?? null;
            $initial = mb_substr($user->name ?? 'U', 0, 1);
            @endphp
            @if($avatar)
            <img src="{{ $avatar }}" alt="{{ $user->name }}" class="avatar-lg">
            @else
            <span class="mypage__initial">{{ $initial }}</span>
            @endif
        </div>
        <div class="mypage__name">{{ $user->name ?? 'ユーザー名' }}</div>
        <a href="{{ route('mypage.profile.edit') }}" class="mypage__edit-btn">プロフィールを編集</a>
    </header>

    {{-- タブ --}}
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

    @php
    $itemsVar = $items ?? ($sells ?? ($buys ?? []));
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
            @endphp
            <li class="tile">
                <a href="{{ route('items.show', $item) }}" class="tile__link">
                    <img class="tile__img"
                        src="{{ $src }}"
                        alt="{{ $item->name }}"
                        loading="lazy"
                        onerror="this.src='{{ asset('images/noimage.svg') }}'">
                    <div class="tile__name">{{ $item->name }}</div>
                </a>
            </li>
            @endforeach
        </ul>

        {{-- ページネーション（LengthAwarePaginator のときだけ表示） --}}
        @if($itemsVar instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mypage__pager">
            {{ $itemsVar->links() }}
        </div>
        @endif
        @endif
    </section>

</div>
@endsection