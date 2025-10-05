<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','COACHTECH')</title>
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @stack('styles')
</head>

<body>
  <header class="site-header">
    <div class="site-header__inner">
      <a href="{{ url('/') }}" class="logo" aria-label="COACHTECH">
        @php /* dd(__FILE__) */ @endphp

        @php
        $svg = file_get_contents(public_path('images/logo.svg'));
        // 先頭の XML/DOCTYPE を削除（インライン描画の邪魔）
        $svg = preg_replace('/<\?xml.*?\?>/s', '', $svg);
          $svg = preg_replace('/<!DOCTYPE.*?>/s', '', $svg);
            echo $svg;
            @endphp
      </a>
      <form action="{{ route('items.index') }}" method="GET" class="header-search">
        <input type="search" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？" aria-label="検索">
        <button type="submit" hidden>検索</button>
      </form>
      <nav class="nav" aria-label="Top Navigation">
        @guest
        <a href="{{ route('login') }}" class="nav__link">ログイン</a>
        <a href="{{ route('login') }}" class="nav__link">マイページ</a>
        <a href="{{ route('login') }}" class="nav__cta">出品</a>
        @endguest
        @auth
        <form action="{{ route('logout') }}" method="POST" class="nav__logout">
          @csrf
          <button type="submit" class="nav__link nav__btn">ログアウト</button>
          <a href="{{ route('mypage.index') }}" class="nav__link">マイページ</a>
        </form>
        <a href="{{ route('sell.create') }}" class="nav__cta">出品</a>
        @endauth
      </nav>
    </div>
  </header>
  <main>@yield('content')</main>
  @stack('scripts')
</body>

</html>