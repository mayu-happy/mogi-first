@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('title', '商品一覧')

@section('content')
<div class="tab-bar">
    <a href="{{ url('/?tab=recommend') }}" class="{{ request('tab') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
    @auth
    <a href="{{ url('/?tab=mylist') }}" class="{{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
    @endauth
</div>

<div class="container py-4">
    <div class="row">
        @forelse ($items as $item)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                {{-- 画像をクリックで詳細ページへ --}}
                <a href="{{ route('items.show', $item) }}" class="card-link" aria-label="『{{ $item->name }}』の詳細へ">
                    <img
                        src="{{ $item->img_url && filter_var($item->img_url, FILTER_VALIDATE_URL) ? $item->img_url : asset('images/noimage.png') }}"
                        alt="{{ $item->name }}"
                        class="card-img-top">
                </a>
                <div class="card-body">
                    <h5 class="card-title">{{ $item->name }}</h5>
                    <p class="card-text">¥{{ number_format($item->price) }}</p>
                </div>
            </div>
        </div>
        @empty
        <p>商品がありません。</p>
        @endforelse
    </div>

    {{-- ページネーション --}}
    <div class="d-flex justify-content-center">
        {{ $items->links() }}
    </div>
</div>
@endsection