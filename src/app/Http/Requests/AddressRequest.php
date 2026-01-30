<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'postcode' => ['required', 'regex:/^\d{3}-\d{4}$/'], //, 'string'いらない？
            'address'  => ['required'], //, 'string'いらない？
        ];
    }

    public function messages(): array
    {
        return [
            'postcode.required' => '郵便番号を入力してください',
            'postcode.regex'    => '郵便番号はハイフンありの8文字で入力してください（例: 123-4567）',
            // 'postcode.string' => '郵便番号は文字列で入力してください',
            'address.required'  => '住所を入力してください',
            // 'address.string' => '住所は文字列で入力してください',
        ];
    }
}
