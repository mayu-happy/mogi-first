@extends('layouts.app')
@section('title', '会員登録')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}?v=2">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth">
        <h1 class="auth__title">会員登録</h1>

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <label for="name">ユーザー名</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}">
            @error('name') <div class="error-message">{{ $message }}</div> @enderror

            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}">
            @error('email') <div class="error-message">{{ $message }}</div> @enderror

            <label for="password">パスワード</label>
            <input id="password" type="password" name="password">
            @error('password') <div class="error-message">{{ $message }}</div> @enderror

            <label for="password_confirmation">確認用パスワード</label>
            <input id="password_confirmation" type="password" name="password_confirmation">
            @error('password_confirmation') <div class="error-message">{{ $message }}</div> @enderror

            <button type="submit" class="btn-primary">登録する</button>
        </form>

        <a class="auth__link" href="{{ route('login') }}">ログインはこちら</a>
    </div>
</div>
@endsection