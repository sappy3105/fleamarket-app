@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
    <div class="mypage">
        {{-- ユーザー情報セクション --}}
        <div class="mypage__profile">
            <div class="mypage__profile-flex">
                <div class="mypage__profile-image">
                    <img src="{{ $profile?->image_path ? asset('storage/' . $profile->image_path) : 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=' }}"
                        alt="">
                </div>
                <h2 class="mypage__profile-name">{{ $user->name }}</h2>
                <a href="{{ route('profile.edit') }}" class="mypage__profile-edit-btn">プロフィールを編集</a>
            </div>
        </div>

        <div class="item-list">
            <div class="item-list__tabs">
                {{-- マイページ内では「出品した商品」タブを表示 --}}
                <span class="item-list__tab is-active">出品した商品</span>
                {{-- お気に入り一覧などを作る場合はここに追加 --}}
            </div>
            <div class="item-list__tabs">
                {{-- マイページ内では「購入した商品」タブを表示 --}}
                <span class="item-list__tab is-active">購入した商品</span>
                {{-- お気に入り一覧などを作る場合はここに追加 --}}
            </div>

            <div class="item-list__grid">
                @foreach ($items as $item)
                    <div class="item-card">
                        <div class="item-card__image">
                            <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}"
                                alt="{{ $item->name }}">
                        </div>
                        <p class="item-card__name">{{ $item->name }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
