@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
    <div class="purchase__container">
        {{-- 左側：商品・支払い・配送先 --}}
        <div class="purchase__left">
            {{-- 商品情報 --}}
            <div class="purchase__item">
                <div class="item__image">
                    <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}"
                        alt="{{ $item->name }}">
                </div>
                <div class="item__detail">
                    <h2>{{ $item->name }}</h2>
                    <p>¥ {{ number_format($item->price) }}</p>
                </div>
            </div>

            {{-- 支払い方法 --}}
            <div class="purchase__section">
                <div class="section__header">
                    <h3>支払い方法</h3>
                </div>
                <div class="select-wrapper">
                    {{-- セッションに保存されている値があれば selected にする --}}
                    <select name="payment_method" form="purchase-form" class="purchase__select" id="payment-select">
                        <option value="" disabled {{ !session("payment_method_{$item->id}") ? 'selected' : '' }}>
                            選択してください</option>
                        <option value="1" {{ session("payment_method_{$item->id}") == 1 ? 'selected' : '' }}>コンビニ払い
                        </option>
                        <option value="2" {{ session("payment_method_{$item->id}") == 2 ? 'selected' : '' }}>カード払い
                        </option>
                    </select>
                </div>
                <div class="purchase-form__error">
                    @error('payment_method')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            {{-- 配送先 --}}
            <div class="purchase__section">
                <div class="section__header">
                    <h3>配送先</h3>
                    <a href="{{ route('purchase.address.edit', $item->id) }}" class="address-edit-link"
                        id="address-edit-link">変更する</a>
                </div>
                <div class="address__content">
                    <p class="address__postcode">〒 {{ $address['postcode'] }}</p>
                    <p class="address__text">{{ $address['address'] }} {{ $address['building'] }}</p>
                </div>
                <div class="purchase-form__error">
                    @error('shipping_address')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        {{-- 右側：確認・購入ボタン --}}
        <div class="purchase__right">
            <table class="confirm__table">
                <tr>
                    <th>商品代金</th>
                    <td><span class="price-amount">¥ {{ number_format($item->price) }}</span></td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td id="display-payment">
                        @if (session("payment_method_{$item->id}") == 1)
                            コンビニ払い
                        @elseif(session("payment_method_{$item->id}") == 2)
                            カード払い
                        @else
                            選択してください
                        @endif
                    </td>
                </tr>
            </table>

            <form action="{{ route('purchase.store', $item->id) }}" method="POST" id="purchase-form">
                @csrf
                <button type="submit" class="purchase__button">購入する</button>
            </form>
        </div>
    </div>

    <script>
        const select = document.getElementById('payment-select');
        const display = document.getElementById('display-payment');
        const addressLink = document.getElementById('address-edit-link');
        const baseAddressUrl = "{{ route('purchase.address.edit', $item->id) }}";

        // 1. セレクトボックス変更時の処理
        select.addEventListener('change', function() {
            const text = this.options[this.selectedIndex].text;
            const value = this.value;
            display.textContent = text;

            // 住所変更リンクに支払い方法をくっつける (例: /address/edit/1?payment_method=2)
            addressLink.href = baseAddressUrl + "?payment_method=" + value;
        });

        // 2. ページ読み込み時に、既に選択されていたらリンクを更新しておく
        if (select.value) {
            addressLink.href = baseAddressUrl + "?payment_method=" + select.value;
        }
    </script>
@endsection
