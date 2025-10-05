@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/item-show.css') }}">
@endpush

@section('title', '商品詳細')

@section('content')
<div class="item-detail">
    <div class="item-detail__grid">

        {{-- 左：画像 --}}
        <div class="item-detail__image">
            @php
            $src = $item->img_url;

            if (!$src) {
            $src = asset('images/noimage.png');
            } elseif (\Illuminate\Support\Str::startsWith($src, ['http://','https://','/storage/'])) {
            // そのまま
            } elseif (\Illuminate\Support\Str::startsWith($src, ['items/'])) {
            $src = asset('storage/' . $src);
            } elseif (\Illuminate\Support\Str::startsWith($src, ['images/'])) {
            $src = asset($src);
            } else {
            $src = asset($src);
            }
            @endphp

            <img src="{{ $src }}" alt="{{ $item->name }}" loading="lazy"> @if ($item->purchase)
            <div class="item-detail__sold">SOLD</div>
            @endif
        </div>

        {{-- 右：情報 --}}
        <div class="item-detail__right">
            <h1 class="item-detail__title">{{ $item->name }}</h1>

            @if (session('message'))
            <div class="flash-message">{{ session('message') }}</div>
            @endif

            <div class="item-detail__brand">ブランド名：{{ $item->brand ?? '－' }}</div>

            <div class="item-detail__price">
                ¥{{ number_format($item->price) }} <span class="item-detail__tax">（税込）</span>
            </div>

            {{-- ★と💬（アウトライン＆下に数） --}}
            <div class="social-stats panel-rail">
                <div class="stat">
                    @auth
                    <form method="POST" action="{{ route('items.likes.toggle', ['item' => $item->id]) }}">
                        @csrf
                        <button type="submit" class="icon like-btn {{ $isLiked ? 'is-liked' : '' }}" title="お気に入り">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 3.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.4L12 18.7 6.2 21.3l1.1-6.4L2.6 10.3l6.5-.9L12 3.5z"
                                    fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="icon like-btn" title="ログインしてお気に入り">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 3.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.4L12 18.7 6.2 21.3l1.1-6.4L2.6 10.3l6.5-.9L12 3.5z"
                                fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                        </svg>
                    </a>
                    @endauth
                    <span class="count">{{ $item->likes_count }}</span>
                </div>

                <div class="stat">
                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 15c1.1 0 2-.9 2-2V7a2 2 0 0 0-2-2H4C2.9 5 2 5.9 2 7v6c0 1.1.9 2 2 2h10l4 4v-4h2z"
                            fill="none" stroke="currentColor" stroke-width="1.6"
                            stroke-linejoin="round" stroke-linecap="round" />
                    </svg>
                    <span class="count">{{ $item->comments_count }}</span>
                </div>
            </div>

            {{-- 購入手続きへ（未ログインはログインへ遷移） --}}
            <a href="{{ auth()->check() ? route('purchase.create', $item) : route('login') }}"
                class="btn btn-purchase btn-full panel-rail">購入手続きへ</a>

            {{-- 商品説明 --}}
            <div class="section panel-rail">
                <h3 class="section-title">商品説明</h3>
                @if (!empty($item->color))
                <p>カラー：{{ $item->color }}</p>
                @endif
                <p class="item-detail__desc">{{ $item->description }}</p>
            </div>

            {{-- 商品の情報 --}}
            <div class="section panel-rail">
                <h3 class="section-title">商品の情報</h3>
                <div class="meta-list">
                    <div class="meta-list__label">カテゴリー</div>
                    <div class="badges">
                        @if($item->categories->isNotEmpty())
                        @foreach($item->categories as $cat)
                        <span class="badge">{{ $cat->name }}</span>
                        @endforeach
                        @else
                        <span class="badge bg-secondary">未分類</span>
                        @endif
                    </div>

                    <div class="meta-list__label">商品の状態</div>
                    <div><span class="badge">{{ $item->condition ?? '－' }}</span></div>
                    <div class="item-detail__price" data-raw-price="{{ $item->price }}">
                        ¥{{ number_format($item->price) }} <span class="item-detail__tax">（税込）</span>
                    </div>
                </div>
            </div>

            {{-- コメント一覧 --}}
            <ul class="cmt-list">
                @foreach ($item->comments as $comment)
                @php
                $user = $comment->user;
                $avatar = $user?->avatar_url ?? asset('images/avatar-default.png');
                $uname = $user?->name ?? '退会ユーザー';
                @endphp

                <li class="cmt">
                    <div class="cmt-meta">
                        <img src="{{ $comment->user?->avatar_url ?? asset('images/avatar-default.png') }}" alt="{{ $comment->user?->name ?? '退会ユーザー' }}">
                        <span class="cmt-name">{{ $uname }}</span>
                    </div>
                    <div class="cmt-bubble">
                        <p class="cmt-text">{{ $comment->body }}</p>
                    </div>
                </li>
                @endforeach
            </ul>
            {{-- コメントフォーム --}}
            <h3 class="cmt-title" style="margin-top:16px;">商品のコメント</h3>

            @auth
            <form class="cmt-form panel-rail" method="POST"
                action="{{ route('items.comments.store', ['item' => $item->id]) }}">
                @csrf

                <textarea id="cmt-body" name="body" class="cmt-textarea" rows="4"
                    placeholder="コメントを入力してください">{{ old('body') }}</textarea>

                @error('body')
                <div class="alert alert-danger" style="margin-top:6px;">{{ $message }}</div>
                @enderror

                <button id="cmt-submit" type="submit" class="btn-cta">コメントを送信する</button>
            </form>
            @else
            <p style="margin-top:8px;">
                コメントするには <a href="{{ route('login') }}">ログイン</a> してください。
            </p>
            @endauth

        </div> {{-- /.item-detail__right --}}
    </div> {{-- /.item-detail__grid --}}
</div> {{-- /.item-detail --}}
@endsection