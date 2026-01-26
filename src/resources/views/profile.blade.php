@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="profile-form">
        <h2 class="profile-form__heading">プロフィール設定</h2>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
            @method('patch')
            @csrf
            <div class="profile-form__image-group">
                <div class="profile-form__image-preview">
                    {{-- 初期状態はグレーの円、選択されたら画像を表示 --}}
                    {{-- デフォルトの画像を準備しない場合は削除<img id="preview" src="{{ asset('images/default-user.png') }}" alt="" --}}
                    {{-- class="profile-form__circle"> --}}
                    <img id="preview"
                        src="{{ $profile->image_path ? asset('storage/' . $profile->image_path) : 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=' }}"
                        alt="" class="profile-form__circle">
                </div>
                <label class="profile-form__image-label" for="image-input" style="cursor: pointer;">
                    画像を選択する
                    <input type="file" name="image_path" id="image-input" accept="image/png,image/jpeg"
                        style="display:none;">
                </label>


                <p class="profile-form__error-message">
                    @error('image_path')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            <div class="profile-form__group">
                <label class="profile-form__label">ユーザー名</label>
                <input type="text" name="name" class="profile-form__input"
                    value="{{ old('name', Auth::user()->name) }}">
                <p class="profile-form__error-message">
                    @error('name')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            <div class="profile-form__group">
                <label class="profile-form__label">郵便番号</label>
                <input type="text" name="postcode" class="profile-form__input"
                    value="{{ old('postcode', $profile->postcode) }}">
                <p class="profile-form__error-message">
                    @error('postcode')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            <div class="profile-form__group">
                <label class="profile-form__label">住所</label>
                <input type="text" name="address" class="profile-form__input"
                    value="{{ old('address', $profile->address) }}">
                <p class="profile-form__error-message">
                    @error('address')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            <div class="profile-form__group">
                <label class="profile-form__label">建物名</label>
                <input type="text" name="building" class="profile-form__input"
                    value="{{ old('building', $profile->building) }}">
            </div>

            <button type="submit" class="profile-form__button-submit">更新する</button>
        </form>
    </div>

    <script>
        // 画像プレビュー機能
        document.getElementById('image-input').addEventListener('change', function(e) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        });
    </script>
@endsection
