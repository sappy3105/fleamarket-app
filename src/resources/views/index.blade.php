@extends('layouts.app')

{{-- @section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection --}}

@section('content')
    <div class="item-list__container">
        <div class="item-list__tabs">
            {{-- おすすめタブ --}}
            <a href="{{ route('item.index', ['tab' => 'all', 'keyword' => request('keyword')]) }}"
                class="item-list__tab {{ $tab !== 'mylist' ? 'is-active' : '' }}">おすすめ</a>

            {{-- マイリストタブ --}}
            <a href="{{ route('item.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
                class="item-list__tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
        </div>

        <div class="item-list__grid">
            @forelse ($items as $item)
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
                                <span class="sold-label">Sold</span>
                            @endif
                        </p>
                    </a>
                </div>
            @empty
                <p class="mylist__empty-message">該当する商品がありません</p>
            @endforelse
        </div>
    </div>
@endsection
