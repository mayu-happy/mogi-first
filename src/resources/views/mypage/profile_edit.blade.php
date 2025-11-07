@extends('layouts.app')
@section('title','プロフィール設定')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage-profile.css') }}?v=7">
@endpush

@if (session('status'))
<div class="profile-success">{{ session('status') }}</div>
@endif


@section('content')
<div class="profile-edit">
    <h1 class="title">プロフィール設定</h1>

    <div class="profile-card">

        {{-- ① アバター変更フォーム（アイコンとボタン） --}}
        <form id="avatarForm"
            action="{{ route('mypage.profile.avatar.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="avatar-block">
            @csrf

            <div class="avatar-wrap">
                <img id="avatarPreview"
                    src="{{ $user->avatar_url }}"
                    class="avatar-lg"
                    alt="プロフィール画像">
            </div>

            <button type="button"
                class="avatar-choose-btn"
                id="avatarChooseBtn">
                画像を選択する
            </button>

            <input
                id="avatarInput"
                type="file"
                name="image"
                accept="image/*"
                class="visually-hidden">

            @error('image')
            <div class="profile-error">{{ $message }}</div>
            @enderror
        </form>

        {{-- ② プロフィール情報フォーム --}}
        <form id="profileForm"
            action="{{ route('mypage.profile.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="profile-form">
            @csrf
            @method('PUT')

            {{-- 入力項目 --}}
            <label class="profile-field">
                <span class="profile-label">ユーザー名</span>
                <input type="text" name="name" class="profile-input"
                    value="{{ old('name', $user->name) }}">
            </label>

            <label class="profile-field">
                <span class="profile-label">郵便番号</span>
                <input type="text" name="postal_code" class="profile-input"
                    value="{{ old('postal_code', $user->postal_code) }}">
            </label>

            <label class="profile-field">
                <span class="profile-label">住所</span>
                <input type="text" name="address" class="profile-input"
                    value="{{ old('address', $user->address) }}">
            </label>

            <label class="profile-field">
                <span class="profile-label">建物名</span>
                <input type="text" name="building" class="profile-input"
                    value="{{ old('building', $user->building) }}">
            </label>

            <button type="submit" class="submit-btn">更新する</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const fileInput = document.getElementById('avatarInput');
        const previewImg = document.getElementById('avatarPreview');
        const form = document.getElementById('avatarForm');
        const chooseBtn = document.getElementById('avatarChooseBtn');

        if (!fileInput || !previewImg || !form || !chooseBtn) return;

        chooseBtn.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                previewImg.src = ev.target.result;
            };
            reader.readAsDataURL(file);

            // form.submit();
        });
    })();
</script>
@endpush