<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required'],
            'description' => ['required', 'max:255'],
            'image_path' => ['required', 'mimes:jpeg,png,jpg'],
            'category_ids' => ['required'],
            'condition' => ['required'],
            'brand_name' => ['nullable'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'image_path.required' => '商品画像を選択してください',
            'image_path.mimes' => '商品画像はjpegもしくはpng形式でアップロードしてください',
            'category_ids.required' => 'カテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '販売価格を数値で入力してください',
            'price.min' => '販売価格を0円以上の金額を入力してください',
        ];
    }
}
