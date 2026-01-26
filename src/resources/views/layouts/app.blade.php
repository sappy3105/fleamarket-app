<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <h1 class="header__heading">
                <a href="/">
                    <img src="{{ asset('images/COACHTECH_headerlogo.png') }}" alt="COACHTECH" class="header__logo">
                </a>
            </h1>

            {{-- ログインと新規登録画面以外表示する --}}
            @unless (request()->routeIs('login', 'register'))
                <div class="header__nav">
                    <form action="/" method="GET" class="header__search">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？"
                            class="header__search-input">
                        <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
                    </form>
                    <nav>
                        <ul class="header__nav-list">
                            <li>
                                <form action="/logout" method="POST">
                                    @csrf
                                    <button type="submit" class="header__nav-link">ログアウト</button>
                                </form>
                            </li>
                            <li><a href="/mypage" class="header__nav-link">マイページ</a></li>
                            <li><a href="/sell" class="header__nav-btn">出品</a></li>
                        </ul>
                    </nav>
                </div>
            @endunless

        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>
