@extends('layouts.app')
@section('title', '商品の出品')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endpush

@section('content')
<div class="sell-container">

    <h1 class="sell-title">商品の出品</h1>

    {{-- 出品フォーム全体 --}}
    <form method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像 --}}
        <section class="block">
            <h2 class="block__title">商品画像</h2>

            <div class="dz-form">
                <label class="dropzone" id="dropzoneLabel">

                    <input
                        id="itemImage"
                        type="file"
                        name="image"
                        accept="image/*"
                        style="display:none;">

                    <button
                        type="button"
                        class="dz-btn"
                        id="selectImageButton">
                        画像を選択する
                    </button>

                    <img
                        id="previewImage"
                        class="thumbs__img"
                        style="display:none;"
                        alt="プレビュー画像">
                </label>

            </div>
        </section>

        {{-- 商品情報 --}}
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
            </div>
        </section>

        {{-- 商品名と説明 --}}
        <section class="block">
            <h2 class="block__title">商品名と説明</h2>

            <div class="field">
                <label class="field__label" for="name">商品名</label>
                <input id="name" type="text" name="name" class="input" value="{{ old('name') }}">
            </div>

            <div class="field">
                <label class="field__label" for="brand">ブランド名</label>
                <input id="brand" type="text" name="brand" class="input" value="{{ old('brand') }}">
            </div>

            <div class="field">
                <label class="field__label" for="description">商品の説明</label>
                <textarea id="description" name="description" class="textarea" rows="4">{{ old('description') }}</textarea>
            </div>

            {{-- 価格 --}}
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

@push('scripts')
<script>
    (function() {
        const fileInput = document.getElementById('itemImage');
        const previewImg = document.getElementById('previewImage'); // <img> プレビュー
        const dropzone = document.getElementById('dropzoneLabel'); // 点線の枠全体
        const selectBtn = document.getElementById('selectImageButton'); // 「画像を選択する」ボタン

        if (!fileInput || !previewImg || !dropzone || !selectBtn) {
            return;
        }

        dropzone.addEventListener('click', function(e) {
            if (e.target === fileInput) return;
            fileInput.click();
        });

        selectBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            fileInput.click();
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onloadend = function(ev) {
                previewImg.src = ev.target.result;
                previewImg.style.display = 'block';
                selectBtn.style.display = 'none';
            };

            reader.readAsDataURL(file);
        });
    })();
</script>
@endpush