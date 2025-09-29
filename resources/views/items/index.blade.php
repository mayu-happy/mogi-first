@extends('layouts.base')
@section('content')
  <h1 class="page-title">商品一覧</h1>
  @isset($kw)<p class="muted">検索ワード: <strong>{{ $kw }}</strong>（{{ $items->total() }}件）</p>@endisset
  @if($items->isEmpty())
    <p>商品がありません。</p>
  @else
    <ul class="grid">
      @foreach($items as $it)
        <li class="card">
          <a class="card__link" href="{{ route('items.show', $it) }}">
            <img class="card__img" src="{{ $it->thumb_url ?? ($it->img_url ?? asset('images/noimage.png')) }}" alt="{{ $it->name }}">
            <div class="card__body">
              <div class="card__name">{{ $it->name }}</div>
              <div class="card__price">¥{{ number_format($it->price) }}</div>
            </div>
          </a>
        </li>
      @endforeach
    </ul>
    <div class="pagination">{{ $items->links() }}</div>
  @endif
@endsection
