@extends('layouts.app')
@section('title', 'ログイン')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}?v=3">
@endpush

@section('content')
<div class="login">
  <div class="login-card">
    <h1 class="login-title">ログイン</h1>

    <form method="POST" action="{{ url('/login') }}" novalidate>
      @csrf

      <label class="login-label">メールアドレス</label>
      <input class="login-input" type="email" name="email" value="{{ old('email') }}">
      @error('email')<div class="error-message">{{ $message }}</div>@enderror

      <label class="login-label">パスワード</label>
      <input class="login-input" type="password" name="password">
      @error('password')<div class="error-message">{{ $message }}</div>@enderror

      <button class="login-btn" type="submit">ログインする</button>
    </form>

    <p class="login-foot">
      <a href="{{ route('register') }}">会員登録はこちら</a>
    </p>
  </div>
</div>
@endsection