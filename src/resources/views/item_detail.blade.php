@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item_detail.css') }}">
@endsection

@section('content')
    <div class="item-detail__container">
        {{-- 左側：商品画像 --}}
        <div class="item-detail__left">
            <div class="item-detail__image-box">
                <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}"
                    alt="{{ $item->name }}">
            </div>
        </div>

        {{-- 右側：商品情報 --}}
        <div class="item-detail__right">
            <h1 class="item-detail__name">
                {{ $item->name }}
                @if ($item->isSold())
                    <span class="sold-label">Sold</span>
                @endif
            </h1>
            <p class="item-detail__brand">{{ $item->brand_name ?? 'ブランド名なし' }}</p>
            <p class="item-detail__price">
                ¥{{ number_format($item->price) }}
                <span>(税込)</span>
            </p>

            {{-- いいね・コメント数 --}}
            <div class="item-detail__icons">
                <div class="item-detail__icon-group">
                    @auth
                        @if ($item->isLikedBy(Auth::user()))
                            {{-- 解除フォーム --}}
                            <form action="{{ route('like.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-button">
                                    <img src="{{ asset('images/heartlogo_pink.png') }}" alt="いいね解除">
                                </button>
                            </form>
                        @else
                            {{-- いいね登録フォーム --}}
                            <form action="{{ route('like.store', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="icon-button">
                                    <img src="{{ asset('images/heartlogo_default.png') }}" alt="いいね登録">
                                </button>
                            </form>
                        @endif
                    @endauth

                    @guest
                        {{-- 未ログイン時はログイン画面へ --}}
                        <a href="{{ route('login') }}" class="icon-button">
                            <img src="{{ asset('images/heartlogo_default.png') }}" alt="いいね">
                        </a>
                    @endguest

                    {{-- いいねカウント表示 --}}
                    <span class="icon-count">{{ $item->likes->count() }}</span>
                </div>

                {{-- コメントカウント機能 --}}
                <div class="item-detail__icon-group">
                    {{-- ログイン済：コメント投稿・一覧画面へ --}}
                    <a href="#comment-area" class="icon-button">
                        <img src="{{ asset('images/comment.png') }}" alt="コメント">
                    </a>

                    {{-- コメントカウント表示 --}}
                    <span class="icon-count">{{ $item->comments->count() }}</span>
                </div>
            </div>

            <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="item-detail__buy-button">購入手続きへ</a>

            <div class="item-detail__section">
                <h2 class="item-detail__section-title">商品説明</h2>
                <p class="item-detail__description">{{ $item->description }}</p>
            </div>

            <div class="item-detail__section">
                <h2 class="item-detail__section-title">商品の情報</h2>
                <div class="item-detail__info-row">
                    <span class="item-detail__info-label">カテゴリー</span>
                    <div class="item-detail__categories">
                        @foreach ($item->categories as $category)
                            <span class="item-detail__category-tag">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="item-detail__info-row">
                    <span class="item-detail__info-label">商品の状態</span>
                    <span class="item-detail__info-value">
                        @switch($item->condition)
                            @case(1)
                                良好
                            @break

                            @case(2)
                                目立った傷や汚れなし
                            @break

                            @case(3)
                                やや傷や汚れあり
                            @break

                            @case(4)
                                状態が悪い
                            @break
                        @endswitch
                    </span>
                </div>
            </div>

            {{-- コメント表示エリア --}}
            <div class="item-detail__section" id="comment-area">
                <h2 class="item-detail__section-title">コメント({{ $item->comments->count() }})</h2>
                @if ($item->comments->isNotEmpty())
                    @foreach ($item->comments as $comment)
                        <div class="comment-item">
                            <div class="comment-item__user">
                                <div class="comment-item__user-image">
                                    <img src="{{ $comment->user->profile?->image_path ? asset('storage/' . $comment->user->profile->image_path) : asset('images/default-user.png') }}"
                                        alt="">
                                </div>
                                <span class="comment-item__user-name">{{ $comment->user->name }}</span>
                            </div>
                            <div class="comment-item__content">
                                {{ $comment->content }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>まだコメントはありません。</p>
                @endif
            </div>

            {{-- コメント投稿フォーム --}}
            <div class="item-detail__comment-form">
                <h3 class="item-detail__comment-label">商品へのコメント</h3>
                <form action="{{ route('comment.store', $item->id) }}" method="POST">
                    @csrf
                    <textarea name="content" class="item-detail__comment-textarea">{{ old('content') }}</textarea>
                    {{-- エラーがあれば表示する --}}
                    <div class="item-detail__comment-error">
                        @error('content')
                            {{ $message }}
                        @enderror
                    </div>

                    {{-- ログインしている時だけボタンを押せるようにする（念のため） --}}
                    @auth
                        <button type="submit" class="item-detail__comment-submit">コメントを送信する</button>
                    @endauth

                    {{-- 未ログインの場合はボタンを押せないか、ログインへの誘導を出す --}}
                    @guest
                        <a href="{{ route('login') }}" class="item-detail__comment-submit">
                            コメントを送信する
                        </a>
                    @endguest
                </form>
            </div>
        </div>
    </div>
@endsection
