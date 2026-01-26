@extends('layouts.app')

@section('content')
    <div class="address__container">
        <h2 class="address-form__heading">住所の変更</h2>
        <form action="{{ route('purchase.address.update', $item->id) }}" method="POST">
            @csrf
            <div class="form__group">
                <label>郵便番号</label>
                <input type="text" name="postcode"
                    value="{{ old('postcode', session("shipping_address_{$item->id}.postcode") ?? Auth::user()->profile->postcode) }}">
                <div class="form__error">
                    @error('postcode')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <label>住所</label>
                <input type="text" name="address"
                    value="{{ old('address', session("shipping_address_{$item->id}.address") ?? Auth::user()->profile->address) }}">
                <div class="form__error">
                    @error('address')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <label>建物名</label>
                <input type="text"
                    name="building"value="{{ old('building', session("shipping_address_{$item->id}.building") ?? Auth::user()->profile->building) }}">
            </div>
            <button type="submit" class="update__btn">更新する</button>
        </form>
    </div>
@endsection
