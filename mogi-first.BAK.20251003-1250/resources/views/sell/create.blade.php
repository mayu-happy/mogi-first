@extends('layouts.app')

@section('title', '商品を出品')

@section('content')
<div class="container">
    <h1>商品を出品する</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name">商品名</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="description">商品説明</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="price">価格（円）</label>
            <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
        </div>

        <div class="mb-3">
            <label>カテゴリー</label>
            <select name="category_ids[]" multiple class="form-control" size="6">
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(in_array($cat->id, old('category_ids', [])))>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="condition">商品の状態</label>
            <select name="condition" class="form-select" required>
                <option value="">選択してください</option>
                <option value="新品">新品</option>
                <option value="良好">良好</option>
                <option value="傷あり">傷あり</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="gender">性別</label>
            <select name="gender" class="form-select">
                <option value="">指定なし</option>
                <option value="メンズ">メンズ</option>
                <option value="レディース">レディース</option>
                <option value="ユニセックス">ユニセックス</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="brand">ブランド名（任意）</label>
            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}">
        </div>

        <div class="mb-3">
            <label for="img_url">商品画像（任意）</label>
            <input type="file" name="img_url" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">出品する</button>
    </form>
</div>
@endsection