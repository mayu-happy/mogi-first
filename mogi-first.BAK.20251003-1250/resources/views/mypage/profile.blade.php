@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div class="mypage-container">

    <div class="user-info">
        <h2 class="user-name">{{ $user->name }}</h2>

        @if ($user->image)
        {{-- 表示用URL（storage付き） --}}
        <img src="{{ asset('storage/'.ltrim(str_replace('public/','',$user->image), '/')) }}" alt="プロフィール画像">
        {{-- テストが見る “生パス” もそのまま出す --}}
        <div>{{ ltrim(str_replace('public/','',$user->image), '/') }}</div>
        @endif
    </div>

    <div class="tab-switch">
        <a class="active">出品した商品</a>
        <a>購入した商品</a>
    </div>

    <div class="item-list">
        @foreach ($sells as $item)
        <div class="item-name">{{ $item->name }}</div>
        @endforeach
    </div>

    <div class="item-list">
        @foreach ($buys as $item)
        <div class="item-name">{{ $item?->name }}</div>
        @endforeach
    </div>

</div>
@endsection