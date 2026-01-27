@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
    <div class="purchase__container">
        <div class="purchase-left">
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
                <select name="payment_method" form="purchase-form" class="purchase__select">
                    <option value="" disabled selected>選択してください</option>
                    <option value="1">コンビニ払い</option>
                    <option value="2">カード払い</option>
                </select>
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
                    <a href="{{ route('purchase.address.edit', $item->id) }}">変更する</a>
                </div>
                <div class="address__content">
                    <p>〒 {{ $address['postcode'] }}</p>
                    <p>{{ $address['address'] }} </p>
                    <p>{{ $address['building'] }}</p>
                </div>
                <div class="purchase-form__error">
                    @error('shipping_address')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        {{-- サイドバー（確認画面） --}}
        <div class="purchase__side">
            <table class="confirm__table">
                <tr>
                    <th>商品代金</th>
                    <td><span class="price-amount">¥ {{ number_format($item->price) }}</span></td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td id="display-payment">選択してください</td>
                </tr>
            </table>

            <form action="{{ route('purchase.store', $item->id) }}" method="POST" id="purchase-form">
                @csrf
                <button type="submit" class="purchase__button">購入する</button>
            </form>
        </div>
    </div>

    <script>
        // プルダウンの選択値を右側の表にリアルタイム反映させるJS
        document.querySelector('.purchase__select').addEventListener('change', function() {
            const text = this.options[this.selectedIndex].text;
            document.getElementById('display-payment').textContent = text;
        });
    </script>
@endsection
