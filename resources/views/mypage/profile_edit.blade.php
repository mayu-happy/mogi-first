@extends('layouts.app')
@section('title','プロフィール設定')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage-profile.css') }}?v=2">
@endpush

@section('content')
<div class="profile-edit">
    <h1 class="title">プロフィール設定</h1>

    {{-- ① アバター専用フォーム（単独） --}}
    {{-- ① アバター用フォーム（単独） --}}
    <form id="avatarForm"
        action="{{ route('mypage.profile.avatar.update') }}"
        method="POST" enctype="multipart/form-data"
        class="avatar-row">
        @csrf

        <div class="avatar-box"> 
            <div class="avatar-wrap">
                <img src="{{ $user->avatar_url }}" class="avatar-lg" alt="">
            </div>
            <button type="submit" class="avatar-save">保存</button> {{-- ★ wrap の外に置く --}}
        </div>

        <label class="btn btn--ghost-red" style="cursor:pointer;">
            画像を選択する
            <input type="file" name="image" accept="image/*" hidden>
        </label>
    </form>

    {{-- ② プロフィール更新フォーム（別フォーム） --}}
    <form action="{{ route('mypage.profile.update') }}" method="POST" class="form">
        @csrf
        @method('PUT')

        <label class="label">ユーザー名
            <input type="text" name="name" class="input" value="{{ old('name', $user->name) }}">
        </label>

        <label class="label">郵便番号
            <input type="text" name="postal_code" class="input" value="{{ old('postal_code', $user->postal_code) }}">
        </label>

        <label class="label">住所
            <input type="text" name="address" class="input" value="{{ old('address', $user->address) }}">
        </label>

        <label class="label">建物名
            <input type="text" name="building" class="input" value="{{ old('building', $user->building) }}">
        </label>

        <button type="submit" class="btn btn--primary" style="width:100%;">更新する</button>
    </form>
</div>
@endsection