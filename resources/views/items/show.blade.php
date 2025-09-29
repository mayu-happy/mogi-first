@extends('layouts.base')
@section('content')
<div class="product">
  <div class="product__media">
    <img class="product__img" src="{{ $item->img_url ?? asset('images/noimage.png') }}" alt="{{ $item->name }}">
  </div>

  <aside>
    <h1 class="product__title">{{ $item->name }}</h1>
    <div class="product__brand">{{ $item->brand ?: 'ブランドなし' }}</div>
    <div class="product__price">¥{{ number_format($item->price) }} <span class="muted" style="font-size:12px">（税込）</span></div>

    <div class="product__section">
      <div class="chips">
        <span class="chip">☆ 3</span>
        <span class="chip">♡ 0</span>
      </div>
    </div>

    <div class="product__section">
      <button class="btn btn--primary" style="width:100%">購入手続きへ</button>
    </div>

    <div class="product__section">
      <h2>商品説明</h2>
      <p class="muted">カラー：グレー</p>
      <p>{{ $item->description ?? '説明はありません。' }}</p>
    </div>

    <div class="product__section">
      <h2>商品の情報</h2>
      <dl class="product__kv">
        <dt>カテゴリー</dt>
        <dd class="badges">
          @foreach(($item->categories ?? []) as $c)
          <span class="badge">{{ $c->name }}</span>
          @endforeach
        </dd>
        <dt>商品の状態</dt>
        <dd>{{ $item->condition ?? '不明' }}</dd>
      </dl>
    </div>

    <div class="product__section">
      <h2>コメント（{{ $item->comments->count() ?? 0 }}）</h2>
      <div class="comments">
        @foreach($item->comments ?? [] as $c)
        <div class="comment">
          <div class="comment__avatar"></div>
          <div>
            <div class="comment__name">{{ $c->user->name ?? 'user' }}</div>
            <div>{{ $c->body }}</div>
          </div>
        </div>
        @endforeach
      </div>

      <form class="form" method="POST" action="#">
        @csrf
        <textarea placeholder="こちらにコメントが入ります。"></textarea>
        <button class="btn btn--primary">コメントを送信する</button>
      </form>
    </div>
  </aside>
</div>
@endsection