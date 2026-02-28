@extends('layouts.app')

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
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
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
