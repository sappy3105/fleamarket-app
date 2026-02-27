<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png'],
            'name'       => ['required', 'max:20'],
            'postcode'   => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'    => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'image_path.image' => '画像ファイルをアップロードしてください',
            'image_path.mimes' => '画像は .jpeg もしくは .png 形式でアップロードしてください',
            'name.required'    => 'お名前を入力してください',
            'name.max'         => 'お名前は20文字以内で入力してください',
            'postcode.required' => '郵便番号を入力してください。',
            'postcode.regex'    => '郵便番号はハイフンありの8文字で入力してください（例: 123-4567）',
            'address.required'  => '住所を入力してください',
        ];
    }
}
