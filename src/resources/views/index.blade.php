@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <div class="item-list">
        <div class="item-list__tabs">
            {{-- おすすめタブ --}}
            <a href="/{{ request('keyword') ? '?keyword=' . request('keyword') : '' }}"
                class="item-list__tab {{ $tab !== 'mylist' ? 'is-active' : '' }}">おすすめ</a>

            {{-- マイリストタブ --}}
            <a href="{{ Auth::check() ? '?tab=mylist' . (request('keyword') ? '&keyword=' . request('keyword') : '') : route('login') }}"
                class="item-list__tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
        </div>

        <div class="item-list__grid">
            @foreach ($items as $item)
                <div class="item-card">
                    <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="item-card__link">
                        <div class="item-card__image">
                            {{-- 画像URLがhttpから始まる場合はそのまま、そうでない場合はstorageから取得 --}}
                            <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}"
                                alt="{{ $item->name }}">
                        </div>
                        <p class="item-card__name">
                            {{ $item->name }}
                            @if ($item->isSold())
                                <span>Sold</span>
                            @endif
                        </p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
