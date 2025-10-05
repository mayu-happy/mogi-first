@extends('layouts.app')

@section('title', '購入履歴')

@section('content')
<div class="container py-4">
    <h1>購入履歴</h1>

    @forelse ($purchases as $purchase)
    <div class="card mb-3">
        <div class="card-body">
            <h5>{{ $purchase->item->name }}</h5>
            <p>購入日：{{ $purchase->purchased_at->format('Y/m/d') }}</p>
            <p>価格：¥{{ number_format($purchase->item->price) }}</p>
        </div>
    </div>
    @empty
    <p class="text-muted">まだ購入履歴がありません。</p>
    @endforelse
</div>
@endsection