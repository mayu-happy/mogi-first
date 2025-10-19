@extends('layouts.app')
@section('title', '商品の出品')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endpush

@section('content')
<div class="sell-container">

    <h1 class="sell-title">商品の出品</h1>

    {{-- 商品画像 --}}
    <section class="block">
        <h2 class="block__title">商品画像</h2>

        <form method="POST" action="{{ route('sell.images.upload') }}" enctype="multipart/form-data" class="dz-form">
            @csrf
            <label class="dropzone">
                <input id="itemImages" type="file" name="images[]" accept="image/*" multiple required>

                {{-- 中央のCTA（:has で画像があれば非表示に） --}}
                <span class="dz-cta">画像を選択する</span>

                {{-- ★ プレビューを枠内に配置（クリック透過） --}}
                @if(!empty($draftImages))
                <ul class="thumbs thumbs--in">
                    @foreach($draftImages as $p)
                    <li class="thumb"><img src="{{ \Illuminate\Support\Facades\Storage::url($p) }}" alt="uploaded"></li>
                    @endforeach
                </ul>
                @endif
            </label>

            {{-- 枠外・控えめの送信ボタン（ファイル選択時だけ表示） --}}
            <button type="submit" class="dz-submit">アップロード</button>
        </form>
    </section>

    {{-- 商品情報 --}}
    <form method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data">
        @csrf

        <section class="block">
            <h2 class="block__title">商品の詳細</h2>

            {{-- カテゴリー --}}
            <div class="field">
                <div class="field__label">カテゴリー</div>
                <div class="chips">
                    @foreach($categories as $cat)
                    <input
                        id="cat-{{ $cat->id }}"
                        class="chip__input"
                        type="checkbox"
                        name="category_ids[]"
                        value="{{ $cat->id }}"
                        {{ in_array($cat->id, old('category_ids', [])) ? 'checked' : '' }}>
                    <label for="cat-{{ $cat->id }}" class="chip">{{ $cat->name }}</label>
                    @endforeach
                </div>
                @error('category_ids') <p class="error">{{ $message }}</p> @enderror
            </div>

            {{-- 商品の状態 --}}
            <div class="field">
                <label class="field__label" for="condition">商品の状態</label>
                <select id="condition" name="condition" class="select">
                    <option value="">選択してください</option>
                    @foreach($conditions as $c)
                    <option value="{{ $c }}" {{ old('condition') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                @error('condition') <p class="error">{{ $message }}</p> @enderror
            </div>
        </section>

        {{-- 商品名と説明 --}}
        <section class="block">
            <h2 class="block__title">商品名と説明</h2>

            <div class="field">
                <label class="field__label" for="name">商品名</label>
                <input id="name" type="text" name="name" class="input" value="{{ old('name') }}">
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label" for="brand">ブランド名</label>
                <input id="brand" type="text" name="brand" class="input" value="{{ old('brand') }}">
                @error('brand') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label" for="description">商品の説明</label>
                <textarea id="description" name="description" class="textarea" rows="4">{{ old('description') }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>

            {{-- 価格（¥ を枠内に） --}}
            <div class="field">
                <label class="field__label" for="price">販売価格</label>
                <div class="yen-input">
                    <span class="yen">¥</span>
                    <input id="price" type="number" name="price" class="input" value="{{ old('price') }}" min="1">
                </div>
            </div>
        </section>

        <div class="actions">
            <button class="btn-primary" type="submit">出品する</button>
        </div>
    </form>
</div>
@endsection