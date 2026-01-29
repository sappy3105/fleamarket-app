@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/exhibition.css') }}">
@endsection

@section('content')
    <div class="sell__container">
        <h2 class="sell__title">商品の出品</h2>

        <form action="{{ route('exhibition.store') }}" method="POST" enctype="multipart/form-data" class="sell__form">
            @csrf

            {{-- 商品画像 --}}
            <div class="sell__section">
                <h3 class="sell__label">商品画像</h3>
                <div class="image-upload__box">
                    <div class="image-upload__preview-wrapper">
                        <img id="preview" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt=""
                            class="image-upload__preview-img">
                    </div>
                    <label class="image-upload__label">
                        <input type="file" name="image_path" id="image-input" class="image-upload__input"
                            accept="image/png, image/jpeg">
                        <span class="image-upload__button">画像を選択する</span>
                    </label>
                </div>
                @error('image_path')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>

            <h2 class="sell__sub-title">商品の詳細</h2>

            {{-- カテゴリー --}}
            <div class="sell__section">
                <h3 class="sell__label">カテゴリー</h3>
                <div class="category__group">
                    @foreach ($categories as $category)
                        <label class="category__label">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                            <span class="category__name">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('category_ids')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 商品の状態 --}}
            <div class="sell__section">
                <h3 class="sell__label">商品の状態</h3>
                <select name="condition" class="sell__select">
                    <option value="" disabled selected>選択してください</option>
                    <option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
                    <option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                    <option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                    <option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
                </select>
                @error('condition')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>

            <h2 class="sell__sub-title">商品名と説明</h2>

            {{-- 商品名 --}}
            <div class="sell__section">
                <h3 class="sell__label">商品名</h3>
                <input type="text" name="name" class="sell__input" value="{{ old('name') }}">
                @error('name')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- ブランド名 --}}
            <div class="sell__section">
                <h3 class="sell__label">ブランド名</h3>
                <input type="text" name="brand_name" class="sell__input" value="{{ old('brand_name') }}">
            </div>

            {{-- 商品の説明 --}}
            <div class="sell__section">
                <h3 class="sell__label">商品の説明</h3>
                <textarea name="description" class="sell__textarea">{{ old('description') }}</textarea>
                @error('description')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 販売価格 --}}
            <div class="sell__section">
                <h3 class="sell__label">販売価格</h3>
                <div class="price__input-container">
                    <span class="price__unit">¥</span>
                    <input type="number" name="price" class="sell__input" value="{{ old('price') }}">
                </div>
                @error('price')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="sell__button">出品する</button>
        </form>
    </div>

    <script>
        // 画像プレビュー機能
        document.getElementById('image-input').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
@endsection
