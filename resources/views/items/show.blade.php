@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/item-show.css') }}?v=2">
@endpush


@section('content')
<div class="product">
  <div class="product__media">
    <img class="product__img"
      src="{{ $item->img_url && filter_var($item->img_url, FILTER_VALIDATE_URL) ? $item->img_url : asset('images/noimage.png') }}"
      alt="{{ $item->name }}">
  </div>

  <aside>
    <h1 class="product__title">{{ $item->name }}</h1>
    <div class="product__brand">{{ $item->brand ?: 'ブランドなし' }}</div>
    <div class="product__price">
      ¥{{ number_format($item->price) }} <span class="muted" style="font-size:12px">（税込）</span>
    </div>

    <div class="product__section">
      <div class="chips" style="display:flex;gap:8px;align-items:center;position:relative;z-index:2;">
        @php
        $liked = false;
        if (auth()->check()) {
        $liked = $item->relationLoaded('likedBy')
        ? $item->likedBy->contains(auth()->id())
        : $item->likedBy()->where('users.id', auth()->id())->exists();
        }
        @endphp

        @auth
        <form action="{{ route('items.likes.toggle', $item) }}" method="post" style="display:inline">
          @csrf
          <button type="submit"
            class="chip"
            aria-pressed="{{ $liked ? 'true' : 'false' }}"
            style="cursor:pointer">
            ★ {{ $item->liked_by_count ?? $item->likedBy()->count() }}
          </button>
        </form>
        @else
        <a class="chip"
          href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}">
          ★ {{ $item->liked_by_count ?? $item->likedBy()->count() }}
        </a>
        @endauth>

        <a class="chip" href="#comments">
          💬 {{ $item->comments_count ?? $item->comments()->count() }}
        </a>
      </div>
    </div>

    <div class="product__section">
      @if (!$item->purchase)
      <a class="btn btn--primary" style="width:100%"
        href="{{ route('purchase.create', $item) }}">購入手続きへ</a>
      @endif
    </div>

    <div class="product__section">
      <h2>商品説明</h2>
      <p class="muted">カラー：{{ $item->color ?? 'グレー' }}</p>
      <p>{{ $item->description ?? '説明はありません。' }}</p>
    </div>

    <div class="product__section">
      <h2>商品の情報</h2>
      <dl class="product__kv">
        <dt>カテゴリー</dt>
        <dd class="badges">
          @forelse(($item->categories ?? []) as $c)
          <span class="badge">{{ $c->name }}</span>
          @empty
          <span class="muted">設定なし</span>
          @endforelse
        </dd>
        <dt>商品の状態</dt>
        <dd>{{ $item->condition ?? '不明' }}</dd>
      </dl>
    </div>

    <div class="product__section">
      <h2>コメント（{{ $item->comments_count ?? ($item->comments?->count() ?? 0) }}）</h2>

      <div class="comments">
        @forelse(($item->comments ?? []) as $c)
        <div class="comment">
          <div class="comment__avatar"></div>
          <div>
            <div class="comment__name">{{ $c->user->name ?? 'user' }}</div>
            <div>{{ $c->body }}</div>
          </div>
        </div>
        @empty
        <p class="muted">こちらにコメントが入ります。</p>
        @endforelse
      </div>

      @auth
      <form class="form" method="POST" action="{{ route('items.comments.store', $item) }}">
        @csrf
        <textarea name="body" rows="3" placeholder="こちらにコメントが入ります。">{{ old('body') }}</textarea>
        @error('body') <div class="text-danger">{{ $message }}</div> @enderror
        <button class="btn btn--primary">コメントを送信する</button>
      </form>
      @endauth
    </div>
  </aside>
</div>
@endsection