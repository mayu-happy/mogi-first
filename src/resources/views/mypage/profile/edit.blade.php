@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}?v={{ filemtime(public_path('css/profile-edit.css')) }}">
@endpush

@section('content')
<div class="profile-page">
    <h1 class="mb-4">プロフィール設定</h1>

    @php
    $avatarUrl = $user->image
    ? \Illuminate\Support\Facades\Storage::url($user->image)
    : asset('images/avatar-placeholder.png');
    @endphp

    <form id="avatarForm" method="POST" action="{{ route('mypage.profile.avatar.update') }}" enctype="multipart/form-data" class="mb-4 profile-edit">
        @csrf
        <div class="avatar-row">
            <div class="avatar-stack">
                <img src="{{ $avatarUrl }}" alt="プロフィール画像" class="avatar-img">
                <input id="avatarInput" type="file" name="image" accept="image/*" class="sr-only">
                <label for="avatarInput" class="avatar-cover" aria-label="画像を選択"></label>
                <button type="submit" class="save-fab save-fab--subtle">保存</button>
            </div>

            {{-- ←ここを右側に並べる --}}
            <label for="avatarInput" class="btn-choose">画像を選択する</label>
        </div>

        @error('image') <p class="text-danger">{{ $message }}</p> @enderror
        @if (session('status')) <p class="text-success">{{ session('status') }}</p> @endif
    </form>
    <form method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data" class="profile-edit">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building) }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-outline-secondary w-100">更新する</button>
    </form>
</div>
@endsection