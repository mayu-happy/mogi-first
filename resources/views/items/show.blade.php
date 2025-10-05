{{-- resources/views/items/show.blade.php --}}
@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/item-show.css') }}?v=3">
@endpush

@section('content')
<div class="product">
  {{-- ===== 左：商品画像 ===== --}}
  <div class="product__media">
    @php
    use Illuminate\Support\Str;

    // 画像URLの決定（http(s) はそのまま / 相対や /storage は asset() / 未設定は noimage）
    $raw = $item->img_url;
    $imgSrc = $raw
    ? (Str::startsWith($raw, ['http://','https://']) ? $raw : asset($raw))
    : asset('images/noimage.png');

    // SOLD 判定（購入テーブルがあれば売り切れ）
    $isSold = $item->relationLoaded('purchase') ? (bool) $item->purchase : $item->purchase()->exists();
    @endphp

    <img class="product__img" src="{{ $imgSrc }}" alt="{{ $item->name }}">
  </div>

  {{-- ===== 右：詳細情報 ===== --}}
  <aside>
    {{-- タイトル・ブランド・価格 --}}
    <h1 class="product__title">{{ $item->name }}</h1>
    <div class="product__brand">{{ $item->brand ?: 'ブランドなし' }}</div>
    <div class="product__price">
      ¥{{ number_format($item->price) }} <span class="muted" style="font-size:12px">（税込）</span>
    </div>

    {{-- いいね＆コメント数 --}}
    <div class="product__section">
      <div class="chips" style="display:flex;gap:8px;align-items:center;position:relative;z-index:2;">
        @php
        $liked = false;
        if (auth()->check()) {
        $liked = $item->relationLoaded('likedBy')
        ? $item->likedBy->contains(auth()->id())
        : $item->likedBy()->where('users.id', auth()->id())->exists();
        }
        $likesCount = $item->liked_by_count ?? $item->likedBy()->count();
        $commentsCount = $item->comments_count ?? $item->comments()->count();
        @endphp

        @auth
        <form action="{{ route('items.likes.toggle', $item) }}" method="post" style="display:inline">
          @csrf
          <button
            type="submit"
            class="chip chip--plain"
            aria-pressed="{{ $liked ? 'true' : 'false' }}"
            title="{{ $liked ? 'いいね済み' : 'いいねする' }}">
            ★ {{ $likesCount }}
          </button>
        </form>
        @else
        <a
          class="chip chip--plain"
          href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
          title="ログインしていいねできます">
          ★ {{ $likesCount }}
        </a>
        @endauth

        <a class="chip" href="#comments" title="コメントへ移動">
          💬 {{ $commentsCount }}
        </a>
      </div>
    </div>

    {{-- 購入ボタン（SOLD 対応） --}}
    <div class="product__section form-block">
      @if ($isSold)
      <button class="btn btn--disabled" disabled aria-disabled="true">SOLD</button>
      @else
      <a class="btn btn--primary" href="{{ route('purchase.create', $item) }}">
        購入手続きへ
      </a>
      @endif
    </div>

    {{-- 商品説明 --}}
    <div class="product__section">
      <h2>商品説明</h2>
      <p class="muted">カラー：{{ $item->color ?? 'グレー' }}</p>
      <p>{{ $item->description ?? '説明はありません。' }}</p>
    </div>

    {{-- 商品情報 --}}
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

    {{-- ===== コメント一覧 & 入力 ===== --}}
    <section id="comments" style="margin-top:24px">
      <h3 class="comments__title">
        コメント <span class="comments__count">({{ $commentsCount }})</span>
      </h3>
      {{-- コメント一覧 --}}
      @forelse ($item->comments as $c)
      @php
      // ユーザーアイコン（User に avatar_url アクセサがある想定 / なければ image を利用）
      $avatar = optional($c->user)->avatar_url
      ?? (optional($c->user)->image ? asset(optional($c->user)->image) : asset('images/avatar-placeholder.png'));
      @endphp
      <div style="display:flex;gap:8px;align-items:flex-start;margin:8px 0;">
        <img
          src="{{ $avatar }}"
          alt="{{ optional($c->user)->name ?? 'user' }}"
          style="width:28px;height:28px;border-radius:999px;object-fit:cover;background:#eee;">
        <div>
          <div style="font-weight:700;font-size:13px;">
            {{ optional($c->user)->name ?? 'user' }}
          </div>
          <div style="font-size:13px;color:#333;">
            {{ $c->body }}
          </div>
        </div>
      </div>
      @empty
      <p class="muted" style="font-size:13px;"></p>
      @endforelse

      {{-- 入力欄（ログイン状態で表示） --}}
      @auth
      <form action="{{ route('items.comments.store', $item) }}" method="post" class="form-block">
        @csrf
        <textarea
          name="body"
          rows="4"
          class="textarea"
          placeholder="商品のコメントを入力">{{ old('body') }}</textarea>

        @error('body')
        <p style="color:#d00;margin-top:6px;">{{ $message }}</p>
        @enderror

        <button type="submit" class="btn btn--primary" style="margin-top:8px;">
          コメントを送信する
        </button>
      </form>
      @else
      <div class="form-block">
        <textarea
          rows="4"
          class="textarea"
          disabled></textarea>
        <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
          class="btn btn--primary" style="margin-top:8px; text-decoration:none;">
          コメントを送信する
        </a>
      </div>
      @endauth
    </section>
  </aside>
</div>
@endsection