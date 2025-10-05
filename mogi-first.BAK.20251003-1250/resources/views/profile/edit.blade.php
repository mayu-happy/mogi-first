@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}?v={{ filemtime(public_path('css/profile.css')) }}">
@endpush

@section('content')
<div class="profile-container">
    <h1>プロフィール設定</h1>

    <form method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- プロフィール画像ブロック --}}
        <div class="avatar-block">
            <div class="avatar">
                <img class="avatar-img" src="{{ $user->avatar_url }}" alt="プロフィール画像">
            </div>

            {{-- 赤フチのボタン。input は隠す --}}
            <label class="btn-choose">
                画像を選択する
                <input type="file" name="image" hidden>
            </label>
        </div>


        <div class="mb-3">
            <label>ユーザー名</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}">
        </div>

        <div class="mb-3">
            <label>郵便番号</label>
            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $user->postal_code ?? '') }}">
        </div>

        <div class="mb-3">
            <label>住所</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $user->address ?? '') }}">
        </div>

        <div class="mb-3">
            <label>建物名</label>
            <input type="text" name="building" class="form-control" value="{{ old('building', $user->building ?? '') }}">
        </div>

        <button type="submit" class="btn btn-primary">更新する</button>
    </form>
</div>
@endsection