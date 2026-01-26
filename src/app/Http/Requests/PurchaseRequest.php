<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'payment_method' => ['required'],
            'shipping_address' => ['required'],
        ];
    }

    protected function prepareForValidation()
    {
        $item_id = $this->route('item_id');
        $session = session("shipping_address_{$item_id}");
        $profile = auth()->user()->profile;

        $finalPostcode = null;
        $finalAddress = null;

        // 1. セッション（変更後の住所）があるか確認
        if ($session) {
            // セッションがある場合は、その中身を信じる（空でもプロフィールは見に行かない）
            $finalPostcode = $session['postcode'] ?? null;
            $finalAddress = $session['address'] ?? null;
        }
        // 2. セッションがない場合のみ、プロフィールの住所を使う
        elseif ($profile) {
            $finalPostcode = $profile->postcode;
            $finalAddress = $profile->address;
        }

        // 最終的な住所が「郵便番号」かつ「住所」ともに埋まっているか判定
        if (!empty(trim($finalPostcode ?? '')) && !empty(trim($finalAddress ?? ''))) {
            $this->merge(['shipping_address' => 'exists']);
        } else {
            $this->merge(['shipping_address' => null]);
        }
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'shipping_address.required' => '配送先を選択してください',
        ];
    }
}
