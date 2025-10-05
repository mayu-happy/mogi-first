@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/register.css') }}">

<div class="register-container">
    <h2>会員登録</h2>

    <form method="POST" action="{{ route('register.submit') }}" novalidate>
        @csrf
        <div>
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
            @error('email')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label>パスワード</label>
            <input type="password" name="password" required>
            @error('password')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-extra">
            <label for="password_confirmation">確認用パスワード</label>
            <input type="password" name="password_confirmation" required>
            @error('password')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit">登録する</button>
    </form>

    <a href="{{ route('login') }}">ログインはこちら</a>
</div>

@endsection