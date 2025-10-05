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
            {{-- 左：ロゴ（SVG） --}}
            <a href="{{ url('/') }}" class="logo" aria-label="COACHTECH" style="display:inline-flex;align-items:center">
                <img
                    src="{{ asset('images/coachtech-logo.svg') . '?v=' . filemtime(public_path('images/coachtech-logo.svg')) }}"
                    alt="COACHTECH"
                    class="site-logo"
                    style="height:24px;display:block">
            </a>

            {{-- 中央：検索 --}}
            <form action="{{ route('items.index') }}" method="get" class="search">
                <input name="q" value="{{ request('q','') }}" placeholder="なにをお探しですか？">
                <input type="hidden" name="tab" value="{{ request('tab','recommend') }}">
            </form>

            {{-- 右：ナビ --}}
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
                {{-- ここを items.create に変更 --}}
                <a href="{{ route('sell.create') }}" class="nav__cta">出品</a>
                @endauth
            </nav>

        </div>
    </header>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>