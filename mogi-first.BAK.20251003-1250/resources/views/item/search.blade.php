@extends('layouts.app')
@section('title','商品検索')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}"> {{-- グリッド流用 --}}
@endpush

@section('content')
<div class="mypage-container">
    <div class="user-info" style="border-bottom:none;">
        <h2 style="margin:0;">「{{ $q }}」の検索結果</h2>
        <span style="margin-left:auto;color:#666;">{{ $total }}件</span>
    </div>

    <div class="item-list" style="margin-top:12px;">
        @forelse ($items as $item)
        <a href="{{ route('items.show', $item) }}" class="item-card">
            <img src="{{ $item->img_url ?? asset('images/noimage.png') }}" alt="{{ $item->name }}" loading="lazy">
            <div class="item-name">{{ $item->name }}</div>
        </a>
        @empty
        <p>該当する商品がありませんでした。</p>
        @endforelse
    </div>

    @if($items instanceof \Illuminate\Pagination\AbstractPaginator)
    <div style="margin-top:16px;">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection