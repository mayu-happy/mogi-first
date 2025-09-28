@extends('layouts.app')
@section('title', '商品の出品')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endpush

@section('content')
<div class="sell-container">

    <h1 class="sell-title">商品の出品</h1>

    {{-- バリデーションエラー --}}
    @if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom:12px;">
        <ul style="margin:0; padding-left:18px;">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
    @endif

    {{-- 画像アップロード（フォームの中に入れる版） --}}
    <form id="sellForm" action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
        @csrf

        <section class="block">
            <label class="block-label">商品画像</label>
            <div class="image-uploader">
                <div class="image-drop">
                    {{-- 編集画面なら既存画像、作成画面は表示なし --}}
                    @if(isset($item) && $item->img_url)
                    <img src="{{ asset($item->img_url) }}" alt="" />
                    @endif

                    <label class="btn-choose" for="image">画像を選択する</label>
                    <input id="image" type="file" name="image" accept="image/*" hidden>
                </div>
                <noscript>
                    <p class="help">※ プレビューは送信後に表示されます（JavaScript無効のため）</p>
                </noscript>
                @error('image') <div class="error">{{ $message }}</div> @enderror
            </div>
        </section>
        {{-- …以下、詳細・名前など続き… --}}

        {{-- 詳細 --}}
        <section class="block">
            <h2 class="block-title">商品の詳細</h2>
            @php
            // 新規: [] ／ 編集: $item->categories->pluck('id')->all()
            $selected = old('category_ids', isset($item) ? $item->categories->pluck('id')->all() : []);
            $categories = $categories->sortBy(['sort_order','id'])->values();
            @endphp

            <fieldset class="cat-fieldset">
                <legend>カテゴリー</legend>

                <div class="cat-group">
                    @foreach ($categories as $cat)
                    <input
                        type="checkbox"
                        id="cat-{{ $cat->id }}"
                        name="category_ids[]"
                        value="{{ $cat->id }}"
                        class="cat-input"
                        {{ in_array($cat->id, $selected ?? [], true) ? 'checked' : '' }} {{-- ← ここを従来の条件式に --}}>
                    <label for="cat-{{ $cat->id }}" class="cat-chip">{{ $cat->name }}</label>
                    @endforeach
                </div>

                @error('category_ids') <p class="error">{{ $message }}</p> @enderror
                @error('category_ids.*') <p class="error">{{ $message }}</p> @enderror
            </fieldset>
            {{-- 商品の状態 --}}
            <div class="form-row">
                <label class="row-label">商品の状態</label>
                <div class="select-wrap">
                    <select name="condition" required>
                        <option value="">選択してください</option>
                        @foreach($conditions as $c)
                        <option value="{{ $c }}" @selected(old('condition')==$c)>{{ $c }}</option>
                        @endforeach
                    </select>
                    @error('condition') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>
        </section>

        {{-- 名前・説明・価格 --}}
        <section class="block">
            <h2 class="block-title">商品名と説明</h2>

            <div class="form-row">
                <label class="row-label">商品名</label>
                <input type="text" name="name" class="input" value="{{ old('name') }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <label class="row-label">ブランド名</label>
                <input type="text" name="brand" class="input" value="{{ old('brand') }}">
            </div>

            <div class="form-row">
                <label class="row-label">商品の説明</label>
                <textarea name="description" class="textarea" rows="5" required>{{ old('description') }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <label class="row-label">販売価格</label>
                <div class="yen-input">
                    <span class="yen">¥</span>
                    <input type="number" name="price" class="input" value="{{ old('price') }}" min="1" step="1" required>
                </div>
                @error('price') <div class="error">{{ $message }}</div> @enderror
            </div>
        </section>

        <div class="actions">
            <button type="submit" class="btn-primary">出品する</button>
        </div>
    </form>
</div>
@endsection