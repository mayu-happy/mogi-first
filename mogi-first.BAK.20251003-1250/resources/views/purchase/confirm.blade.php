@extends('layouts.app')

@section('title', '購入確認')

@section('content')
<div class="container py-4">
    <h1>購入確認</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $item->name }}</h5>
            <p class="card-text">価格：¥{{ number_format($item->price) }}</p>
            <img src="{{ $item->img_url ?? asset('images/noimage.png') }}" alt="{{ $item->name }}" class="img-fluid mb-3">

            <form method="POST" action="{{ route('purchase.complete', ['item_id' => $item->id]) }}">
                @csrf
                <button type="submit" class="btn btn-danger">購入する</button>
            </form>
        </div>
    </div>
</div>
@endsection