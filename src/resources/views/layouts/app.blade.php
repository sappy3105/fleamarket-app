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

            @yield('link') {{-- ここに検索窓とナビが入る --}}
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>
