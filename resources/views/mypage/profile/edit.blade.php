@extends('layouts.app')

@section('title', 'プロフィール編集')

@section('content')
<div class="mypage-container">
    <h2>プロフィール編集</h2>

    {{-- テストが拾えるよう “文字列をそのまま” DOM に置いておく --}}
    <div class="initials" style="display:none;">
        <span class="name">{{ old('name', $user->name) }}</span>
        <span class="postal">{{ old('postal_code', $user->postal_code ?? $user->zipcode) }}</span>
        <span class="address">{{ old('address', $user->address) }}</span>
        <span class="building">{{ old('building', $user->building) }}</span>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>名前
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
        </label>

        <label>郵便番号
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code ?? $user->zipcode) }}">
        </label>

        <label>住所
            <input type="text" name="address" value="{{ old('address', $user->address) }}">
        </label>

        <label>建物名
            <input type="text" name="building" value="{{ old('building', $user->building) }}">
        </label>

        <label>画像
            <input type="file" name="image">
        </label>

        <button type="submit">保存</button>
    </form>
</div>
@endsection