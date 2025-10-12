@extends('layouts.app')
@section('title', '会員登録')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}?v=1">
@endpush

@section('content')
<div class="auth">
    <h1 class="auth__title">会員登録</h1>

    <form method="POST" action="{{ route('register') }}" class="auth__form" novalidate>
        @csrf

        <div class="field">
            <label for="name" class="field__label">ユーザー名</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" class="input">
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="email" class="field__label">メールアドレス</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="input">
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="password" class="field__label">パスワード</label>
            <input id="password" name="password" type="password" class="input">
            @error('password') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation" class="field__label">確認用パスワード</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                class="input" autocomplete="new-password" required>
            @error('password_confirmation') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn-primary w-100">登録する</button>
    </form>
</div>
@endsection