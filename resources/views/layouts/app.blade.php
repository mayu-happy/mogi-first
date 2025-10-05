<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','COACHTECH')</title>

    {{-- 共通スタイル --}}
    <link rel="stylesheet" href="{{ asset('css/common.css') }}?v=3">

    {{-- 画面固有スタイル（各ビューから @push('styles') で追加） --}}
    @stack('styles')
</head>

<body>

    <header class="site-header">
        <div class="container site-header__inner">
            {{-- 左：ロゴ --}}
            <a href="{{ url('/') }}" class="logo" aria-label="COACHTECH">
                <img
                    src="{{ asset('images/coachtech-logo.svg') }}"
                    alt="COACHTECH">
            </a>

            {{-- 中央：検索 --}}
            <form action="{{ route('items.index') }}" method="get" class="search" role="search">
                <input name="q" value="{{ request('q','') }}" placeholder="なにをお探しですか？">
                <input type="hidden" name="tab" value="{{ request('tab','recommend') }}">
            </form>

            {{-- 右：ナビ --}}
            <nav class="nav" aria-label="Top Navigation">
                {{-- ゲスト時 --}}
                @guest
                <a href="{{ route('login') }}" class="nav__link">ログイン</a>
                <a href="{{ route('login') }}" class="nav__link">マイページ</a>
                <a href="{{ route('sell.create') }}" class="nav__cta nav__cta--light">出品</a>
                @endguest

                {{-- ログイン時 --}}
                @auth
                <form action="{{ route('logout') }}" method="POST" class="nav__logout">
                    @csrf
                    <button type="submit" class="nav__link nav__btn">ログアウト</button>
                    <a href="{{ route('mypage.index') }}" class="nav__link">マイページ</a>
                </form>
                <a href="{{ route('sell.create') }}" class="nav__cta nav__cta--light">出品</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="content container">
        @yield('content')
    </main>
</body>

</html>