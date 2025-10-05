@extends('layouts.app')

@section('title', 'マイページ')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush

@section('content')
<div class="mypage-container">
    {{-- マイページのユーザー情報ヘッダー --}}
    <div class="user-info">
        <div class="user-main">
            <div class="avatar-circle">
                @if($user->image)
                <img src="{{ $user->avatar_url }}" alt="プロフィール画像">
                @else
                {{-- 画像なしの場合は何も入れなくてOK（グレー丸のまま） --}}
                @endif
            </div>

            <h2 class="user-name">{{ $user->name }} さん</h2>
        </div>

        <a href="{{ route('mypage.profile') }}" class="btn-edit">プロフィールを編集</a>
    </div>

    <div class="tab-switch">
        <a href="{{ route('mypage.sell') }}" class="{{ request()->routeIs('mypage.sell') ? 'active' : '' }}">
            出品した商品
        </a>

        <a href="{{ route('mypage.buy') }}" class="{{ request()->routeIs('mypage.buy') ? 'active' : '' }}">
            購入商品
            <span class="sr-only">購入した商品</span>
        </a>
    </div>
    <div class="item-list">
        @forelse ($items as $item)
        @php
        $sold = (bool) $item->purchase;

        // 画像URLを安全に決定（S3/絶対URL/ローカルstorage/相対パスすべて対応）
        $src = $item->img_url;
        if (!$src) {
        $src = asset('images/noimage.png');
        } elseif (\Illuminate\Support\Str::startsWith($src, ['http://','https://','/storage/'])) {
        // そのまま使う
        } else {
        // 相対パス（例: "items/xxx.jpg"）は public/storage を付与
        $src = asset('storage/'.$src);
        }
        @endphp

        @if ($sold)
        {{-- 購入済み：リンク無効（クリックできないカード） --}}
        <div class="item-card is-disabled" aria-disabled="true">
            <div class="thumb">
                <img src="{{ $src }}" alt="商品画像" loading="lazy">
                <span class="sold">SOLD</span>
            </div>
            <div class="item-name">{{ $item->name }}</div>
        </div>
        @else
        {{-- 未購入：通常リンク --}}
        <a href="{{ route('items.show', $item) }}" class="item-card" aria-label="『{{ $item->name }}』の詳細へ">
            <div class="thumb">
                <img src="{{ $src }}" alt="商品画像" loading="lazy">
            </div>
            <div class="item-name">{{ $item->name }}</div>
        </a>
        @endif
        @empty
        <p>商品がありません。</p>
        @endforelse
    </div>
</div>
@endsection