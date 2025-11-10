@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/item-show.css') }}?v=4">
@endpush

@section('title', $item->name)

@section('content')
<div class="item-detail">
  <div class="item-detail__grid">

    {{-- 左：画像 --}}
    <figure class="media">
      <img class="media__img"
        src="{{ $item->image_url ?: asset('images/noimage.svg') }}"
        alt="{{ $item->name }}"
        onerror="this.src='{{ asset('images/noimage.svg') }}'">
    </figure>

    {{-- 右：情報 --}}
    <aside class="panel">
      <h1 class="title">{{ $item->name }}</h1>
      @if($item->brand)
      <div class="brand">{{ $item->brand }}</div>
      @endif

      {{-- ① 価格 --}}
      <div class="price">
        ¥{{ number_format($item->price) }} <span class="muted">（税込）</span>
      </div>

      {{-- ② ★と吹き出し（ミニ統計） --}}
      <div class="mini-stats">
        @auth
        <form action="{{ route('items.likes.toggle', $item) }}" method="post" class="mini-stat-form">
          @csrf
          <button type="submit"
            class="mini-stat {{ ($liked ?? false) ? 'is-liked' : '' }}"
            aria-pressed="{{ ($liked ?? false) ? 'true' : 'false' }}"
            title="お気に入り">
            <svg viewBox="0 0 24 24" class="mini-stat__icon" aria-hidden="true">
              <path d="M12 17.3l-6.2 3.7 1.7-7.3L2 9.2l7.4-.6L12 2l2.6 6.6 7.4.6-5.5 4.5 1.7 7.3z" />
            </svg>
            <span class="mini-stat__num">{{ $item->liked_by_count ?? 0 }}</span>
          </button>
        </form>
        @else
        <a href="{{ route('login') }}" class="mini-stat" title="お気に入り">
          <svg viewBox="0 0 24 24" class="mini-stat__icon" aria-hidden="true">
            <path d="M12 17.3l-6.2 3.7 1.7-7.3L2 9.2l7.4-.6L12 2l2.6 6.6 7.4.6-5.5 4.5 1.7 7.3z" />
          </svg>
          <span class="mini-stat__num">{{ $item->liked_by_count ?? 0 }}</span>
        </a>
        @endauth

        <div class="mini-stat" title="コメント数">
          <svg viewBox="0 0 24 24" class="mini-stat__icon" aria-hidden="true">
            <path d="M20 3H4a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h3v3l4.5-3H20a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z" />
          </svg>
          <span class="mini-stat__num">{{ $item->comments_count ?? 0 }}</span>
        </div>
      </div>

      {{-- ③ 購入ボタン（mini-stats の下に移動） --}}
      @php
      $purchaseUrl = auth()->check() ? route('purchase.create', $item) : route('login');
      @endphp
      <a href="{{ $purchaseUrl }}" class="btn btn--primary" style="width:100%;margin-top:12px;">
        購入手続きへ
      </a>

      {{-- ★ ここにあった <hr> は削除 --}}
      {{-- <hr> --}}

      {{-- ④ 商品説明（罫線なしで続く） --}}
      <section class="desc" style="margin-top:16px;">
        <h2>商品説明</h2>
        <p class="body">{{ $item->description }}</p>
      </section>

      <section class="meta">
        <h2>商品の情報</h2>

        {{-- カテゴリー：無い場合は「なし」 --}}
        @php
        $hasCategories = $item->relationLoaded('categories')
        ? $item->categories->isNotEmpty()
        : $item->categories()->exists();
        @endphp
        <div class="meta-row">
          <div class="meta-label">カテゴリー</div>
          <div class="meta-value">
            @if ($hasCategories)
            @foreach($item->categories as $c)
            <span class="chip">{{ $c->name }}</span>
            @endforeach
            @else
            <span class="muted">なし</span>
            @endif
          </div>
        </div>

        {{-- 商品の状態 --}}
        <div class="meta-row">
          <div class="meta-label">商品の状態</div>
          <div class="meta-value">{{ $item->condition ?? 'ー' }}</div>
        </div>
      </section>

      <section class="comments">
        <h2>コメント <span class="muted">({{ $item->comments_count ?? 0 }})</span></h2>

        {{-- ▼ コメント一覧 --}}
        @if(isset($comments) && $comments->count())
        <ul class="cmt-list">
          @foreach($comments as $cm)
          <li class="cmt">
            <div class="cmt__head">
              {{-- ★ここだけで avatar_url を使う --}}
              <img
                src="{{ optional($cm->user)->avatar_url ?? asset('images/default-user.png') }}"
                alt="{{ optional($cm->user)->name ?? '名無し' }}"
                class="cmt__avatar">

              <span class="cmt__name">{{ optional($cm->user)->name ?? '名無し' }}</span>
            </div>

            <div class="cmt__bubble">{{ $cm->body }}</div>
          </li>
          @endforeach
        </ul>
        @endif
        <div class="field-label" style="font-weight:700;margin-bottom:6px;">商品へのコメント</div>

        {{-- ▼ 投稿フォーム --}}
        @auth
        <form method="POST" action="{{ route('items.comments.store', $item) }}" class="mt-8">
          @csrf
          <textarea name="body" rows="4" class="input" maxlength="255" placeholder="コメントを入力">{{ old('body') }}</textarea>
          <button class="btn btn--primary" type="submit" style="width:100%;margin-top:8px">コメントを送信する</button>
        </form>
        @else
        <form action="{{ route('login') }}" method="GET" class="mt-8">
          <textarea rows="4" class="input" placeholder="" disabled></textarea>
          <button class="btn btn--primary" type="submit" style="width:100%;margin-top:8px">コメントを送信する</button>
        </form>
        @endauth
      </section>
    </aside>

  </div>
</div>
@endsection