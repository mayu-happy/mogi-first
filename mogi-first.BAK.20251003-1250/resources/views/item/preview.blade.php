@extends('layouts.app')
@section('title','出品内容の確認')

@section('content')
<div class="item-detail" style="max-width:720px;margin:0 auto;">
    <h2 style="margin:8px 0 16px;">出品内容の確認</h2>

    <div class="section">
        <div style="display:flex; gap:16px; align-items:flex-start;">
            <div>
                @if($tmpUrl)
                <img src="{{ $tmpUrl }}" alt="プレビュー" style="width:220px;height:auto;border:1px solid #ddd;border-radius:8px;background:#fff;">
                @else
                <div style="width:220px;height:160px;border:1px dashed #ccc;border-radius:8px;display:grid;place-items:center;color:#777;">
                    画像なし
                </div>
                @endif
            </div>
            <div>
                <div><strong>商品名：</strong>{{ $data['name'] }}</div>
                <div><strong>ブランド：</strong>{{ $data['brand'] ?? '—' }}</div>
                <div><strong>カテゴリー：</strong>{{ $category->name ?? '—' }}</div>
                <div><strong>状態：</strong>{{ $data['condition'] }}</div>
                <div><strong>価格：</strong>¥ {{ number_format($data['price']) }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">商品の説明</div>
        <p style="white-space:pre-wrap;">{{ $data['description'] }}</p>
    </div>

    <div class="section" style="display:flex; gap:10px;">
        {{-- 確定 --}}
        <form action="{{ route('sell.store') }}" method="POST">
            @csrf
            <button type="submit" class="btn-cta">この内容で出品する</button>
        </form>

        {{-- 戻って修正（下書きを使ってフォーム再表示） --}}
        <a href="{{ route('sell.create') }}" class="btn-ghost" style="line-height:44px;padding:0 14px;border:1px solid #ddd;border-radius:8px;text-decoration:none;">戻って修正する</a>
    </div>
</div>
@endsection