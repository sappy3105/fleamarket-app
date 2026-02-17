@extends('layouts.app')

{{-- @section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection --}}

@section('content')
    <div class="mypage__container">
        {{-- ユーザー情報セクション --}}
        <div class="mypage__profile">
            <div class="mypage__profile-left">
                <div class="mypage__profile-image">
                    <img src="{{ $profile?->image_path ? asset('storage/' . $profile->image_path) : 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=' }}"
                        alt="">
                </div>
                <h2 class="mypage__profile-name">{{ $user->name }}</h2>
            </div>
            <a href="{{ route('profile.edit') }}" class="mypage__profile-edit-button">プロフィールを編集</a>
        </div>

        {{-- タブメニュー --}}
        <div class="mypage__tabs">
            <a href="?page=sell{{ request('keyword') ? '&keyword=' . request('keyword') : '' }}"
                class="mypage__tab {{ $page === 'sell' ? 'is-active' : '' }}">出品した商品</a>
            <a href="?page=buy{{ request('keyword') ? '&keyword=' . request('keyword') : '' }}"
                class="mypage__tab {{ $page === 'buy' ? 'is-active' : '' }}">購入した商品</a>
        </div>

        {{-- 商品一覧グリッド --}}
        <div class="mypage__grid">
            @forelse ($items as $item)
                <div class="item-card">
                    <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="item-card__link">
                        <div class="item-card__image">
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
                <p class="mypage__empty-message">該当する商品がありません</p>
            @endforelse
        </div>
    </div>
@endsection
