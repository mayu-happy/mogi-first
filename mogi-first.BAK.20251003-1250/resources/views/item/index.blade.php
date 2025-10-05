@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('title', '商品一覧')

@section('content')
<div class="list-container">

    {{-- タブ --}}
    <div class="tab-bar">
        <a href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}"
            class="{{ $tab === 'recommend' ? 'active' : '' }}"
            @if($tab==='recommend' ) aria-current="page" @endif>おすすめ</a>

        <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
            class="{{ $tab === 'mylist' ? 'active' : '' }}"
            @if($tab==='mylist' ) aria-current="page" @endif>マイリスト</a>
    </div>

    {{-- 一覧グリッド --}}
    <div class="list-container">

        <div class="items-grid">
            @foreach ($items as $item)
            @php
            $src = $item->img_url ?: asset('images/noimage.png');
            @endphp

            <a href="{{ route('items.show',$item) }}" class="item-card">
                <div class="thumb">
                    <img src="{{ $src }}" alt="{{ $item->name }}">
                    @if($item->purchase)<div class="sold">SOLD</div>@endif
                </div>
                <div class="name">{{ $item->name }}</div>
            </a>
            @endforeach
        </div>
    </div>

</div>
@endsection