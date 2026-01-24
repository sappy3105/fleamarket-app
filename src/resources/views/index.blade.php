@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('link')
    <div class="header__nav">
        <form action="/" method="GET" class="header__search">
            <input type="text" name="keyword" placeholder="なにをお探しですか？" class="header__search-input">
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
@endsection

@section('content')
    <div class="item-list">
        <div class="item-list__tabs">
            <a href="{{ route('item.index') }}" class="item-list__tab {{ $tab !== 'mylist' ? 'is-active' : '' }}">おすすめ</a>
            <a href="{{ route('item.index', ['tab' => 'mylist']) }}"
                class="item-list__tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
        </div>

        <div class="item-list__grid">
            @foreach ($items as $item)
                <div class="item-card">
                    <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="item-card">
                        <div class="item-card__image">
                            {{-- 画像URLがhttpから始まる場合はそのまま、そうでない場合はstorageから取得 --}}
                            <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}"
                                alt="{{ $item->name }}">
                        </div>
                        <p class="item-card__name">{{ $item->name }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
