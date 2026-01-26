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
            'image_path' => ['nullable', 'image', 'mimes:jpeg,png'], // jpegまたはpng
            'name'       => ['required', 'string', 'max:20'],       // 必須、20文字以内
            'postcode'   => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], // 必須、ハイフンあり8文字
            'address'    => ['required', 'string'],     // 必須
        ];
    }

    public function messages(): array
    {
        return [
            'image_path.image' => '指定されたファイルが画像ではありません',
            'image_path.mimes' => '画像の拡張子は .jpeg または .png を指定してください',
            'name.required'    => 'お名前を入力してください',
            'name.max'         => 'お名前は20文字以内で入力してください',
            'name.string' => 'お名前は文字列で入力してください',
            'postcode.required' => '郵便番号を入力してください。',
            'postcode.regex'    => '郵便番号はハイフンありの8文字で入力してください（例: 123-4567）',
            'postcode.string' => '郵便番号は文字列で入力してください',
            'address.required'  => '住所を入力してください',
            'address.string' => '住所は文字列で入力してください',
        ];
    }
}
