@extends('layouts.app')

@section('content')
    <div class="profile-form">
        <h2 class="profile-form__title">プロフィール設定</h2>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
            @method('patch')
            @csrf
            <div class="profile-form__image-group">
                <div class="profile-form__image-preview">
                    {{-- srcの優先順位: 1. old(エラー復帰) 2. DB保存済み画像 3. 空の透過画像(ダミー) --}}
                    <img id="preview"
                        src="{{ old('image_preview_data') ?? ($profile->image_path ? asset('storage/' . $profile->image_path) : 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=') }}"
                        alt="" class="profile-form__circle">
                </div>
                <label class="profile-form__image-label" for="image-input">
                    画像を選択する
                    <input type="file" name="image_path" id="image-input" accept="image/png,image/jpeg">
                </label>

                {{-- データの受け渡し用隠しフィールド --}}
                <input type="hidden" name="image_preview_data" id="image-preview-data"
                    value="{{ old('image_preview_data') }}">
            </div>

            <div class="profile-form__error-message">
                @error('image_path')
                    {{ $message }}
                @enderror

                {{-- 他の項目でエラーが出て画像がリセットされた時の注釈 --}}
                @if ($errors->any() && !$errors->has('image_path') && old('image_preview_data'))
                    <p class="profile-form__error-note">
                        ※プレビューが表示されていますが、保存のため再度画像を選択してください。
                    </p>
                @endif
            </div>

            <div class="profile-form__group">
                <label class="profile-form__label">ユーザー名</label>
                <input type="text" name="name" class="profile-form__input"
                    value="{{ old('name', Auth::user()->name) }}">
                <div class="profile-form__error-message">
                    @error('name')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="profile-form__group">
                <label class="profile-form__label">郵便番号</label>
                <input type="text" name="postcode" class="profile-form__input"
                    value="{{ old('postcode', $profile->postcode) }}">
                <div class="profile-form__error-message">
                    @error('postcode')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="profile-form__group">
                <label class="profile-form__label">住所</label>
                <input type="text" name="address" class="profile-form__input"
                    value="{{ old('address', $profile->address) }}">
                <div class="profile-form__error-message">
                    @error('address')
                        {{ $message }}
                    @enderror
                </div>
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
        document.getElementById('image-input').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const result = e.target.result;
                    const preview = document.getElementById('preview');
                    const hiddenInput = document.getElementById('image-preview-data');

                    preview.src = result;

                    if (hiddenInput) {
                        hiddenInput.value = result;
                    }
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
@endsection
