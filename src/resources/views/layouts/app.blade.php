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
                    <form action="{{ request()->url() }}" method="GET" class="header__search">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？"
                            class="header__search-input">
                        @if (Route::is('mypage'))
                            {{-- マイページにいるときは 'page' を送る --}}
                            <input type="hidden" name="page" value="{{ request('page', 'sell') }}">
                        @elseif (Route::is('item.index'))
                            {{-- 商品一覧ページにいるときは 'tab' を送る --}}
                            <input type="hidden" name="page" value="{{ request('tab', 'all') }}">
                        @endif
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

    <main class="main">
        <div class="main__inner">
            @yield('content')
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const allWrappers = document.querySelectorAll('.select-wrapper');

            allWrappers.forEach(wrapper => {
                const realSelect = wrapper.querySelector('.custom-select-input');
                const trigger = wrapper.querySelector('.select-trigger');
                const triggerText = wrapper.querySelector('.trigger-text');
                const options = wrapper.querySelectorAll('.custom-option');

                if (!trigger || !realSelect) return;

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    allWrappers.forEach(w => {
                        if (w !== wrapper) w.classList.remove('is-open');
                    });
                    wrapper.classList.toggle('is-open');
                });

                options.forEach(option => {
                    option.addEventListener('click', function() {
                        const val = this.getAttribute('data-value');
                        const text = this.textContent;

                        realSelect.value = val;
                        triggerText.textContent = text;
                        triggerText.classList.remove('is-placeholder');

                        options.forEach(opt => opt.classList.remove('selected'));
                        this.classList.add('selected');

                        // 既存のイベント（支払い方法反映など）を連動させる
                        realSelect.dispatchEvent(new Event('change'));

                        wrapper.classList.remove('is-open');
                    });
                });
            });

            document.addEventListener('click', () => {
                allWrappers.forEach(w => w.classList.remove('is-open'));
            });
        });
    </script>

</body>

</html>
