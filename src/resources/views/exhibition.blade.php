@extends('layouts.app')

{{-- @section('css')
    <link rel="stylesheet" href="{{ asset('css/exhibition.css') }}">
@endsection --}}

@section('content')
    <div class="exhibition__container">
        <h2 class="exhibition__title">商品の出品</h2>

        <form action="{{ route('exhibition.store') }}" method="POST" enctype="multipart/form-data" class="exhibition__form">
            @csrf

            {{-- 商品画像 --}}
            <div class="exhibition__section">
                <h4 class="exhibition__label">商品画像</h4>
                <div class="image-upload__box">
                    <img id="preview" src="" alt="" class="image-upload__preview">
                    <label class="image-upload__label">
                        <input type="file" name="image_path" id="image-input" class="image-upload__input"
                            accept="image/png, image/jpeg">
                        <span class="image-upload__button">画像を選択する</span>
                    </label>
                </div>
                <div class="exhibition__error-message">
                    @error('image_path')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <h3 class="exhibition__sub-title">商品の詳細</h3>

            {{-- カテゴリー --}}
            <div class="exhibition__section">
                <h4 class="exhibition__label">カテゴリー</h4>
                <div class="category__group">
                    @foreach ($categories as $category)
                        <label class="category__label">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                            <span class="category__name">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="exhibition__error-message">
                    @error('category_ids')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            {{-- 商品の状態 --}}
            <div class="exhibition__section">
                <h4 class="exhibition__label">商品の状態</h4>
                <div class="select-wrapper exhibition__select-wrapper">
                    {{-- 実際のselect --}}
                    <select name="condition" class="custom-select-input hidden-select">
                        <option value="" disabled selected>選択してください</option>
                        <option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
                        <option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                        <option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                        <option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
                    </select>

                    {{-- カスタム部分 --}}
                    <div class="custom-select-ui">
                        <div class="select-trigger">
                            @php
                                $conditionMap = [
                                    1 => '良好',
                                    2 => '目立った傷や汚れなし',
                                    3 => 'やや傷や汚れあり',
                                    4 => '状態が悪い',
                                ];
                                $currentCondition = old('condition');
                                $conditionLabel = $conditionMap[$currentCondition] ?? '選択してください';
                            @endphp
                            <span
                                class="trigger-text {{ !$currentCondition ? 'is-placeholder' : '' }}">{{ $conditionLabel }}</span>
                        </div>
                        <div class="custom-options">
                            @foreach ($conditionMap as $val => $text)
                                <div class="custom-option {{ $currentCondition == $val ? 'selected' : '' }}"
                                    data-value="{{ $val }}">{{ $text }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="exhibition__error-message">
                    @error('condition')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <h3 class="exhibition__sub-title">商品名と説明</h3>

            {{-- 商品名 --}}
            <div class="exhibition__section">
                <h4 class="exhibition__label">商品名</h4>
                <input type="text" name="name" class="exhibition__input" value="{{ old('name') }}">
                <div class="exhibition__error-message">
                    @error('name')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            {{-- ブランド名 --}}
            <div class="exhibition__section">
                <h4 class="exhibition__label">ブランド名</h4>
                <input type="text" name="brand_name" class="exhibition__input" value="{{ old('brand_name') }}">
            </div>

            {{-- 商品の説明 --}}
            <div class="exhibition__section">
                <h4 class="exhibition__label">商品の説明</h4>
                <textarea name="description" class="exhibition__textarea">{{ old('description') }}</textarea>
                <div class="exhibition__error-message">
                    @error('description')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            {{-- 販売価格 --}}
            <div class="exhibition__section">
                <h4 class="exhibition__label">販売価格</h4>
                <div class="price__input-container">
                    <span class="price-input__unit">¥</span>
                    <input type="number" name="price" class="exhibition__input exhibition__input--price"
                        value="{{ old('price') }}">
                </div>
                <div class="exhibition__error-message">
                    @error('price')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <button type="submit" class="exhibition__submit-button">出品する</button>
        </form>
    </div>

    <script>
        document.getElementById('image-input').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
@endsection
