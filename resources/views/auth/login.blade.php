@extends('layouts.app')

@section('title', 'ログイン')

@section('content')

<link rel="stylesheet" href="{{ asset('css/login.css') }}">

<div class="login-container">
  <h2>ログイン</h2>

  {{-- ログインフォーム --}}
  <form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    <label>メールアドレス</label>
    <input type="email" name="email" value="{{ old('email') }}">
    @error('email')<div class="error-message">{{ $message }}</div>@enderror

    <label>パスワード</label>
    <input type="password" name="password">
    @error('password')<div class="error-message">{{ $message }}</div>@enderror

    <button type="submit">ログイン</button>
  </form>

  <div class="mt-3">
    <a href="{{ route('register') }}">会員登録はこちら</a>
  </div>
</div>

@endsection