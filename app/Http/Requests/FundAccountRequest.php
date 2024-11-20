<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FundAccountRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return   [
            'name' => "required",
            'balance' => "required",
            'account_number' => "required",
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'لطفا نام صندوق را وارد کنید!',
            'balance.required' => 'لطفا موجودی صندوق را وارد کنید!',
            'account_number.required' => 'لطفا شماره حساب صندوق را وارد کنید!',
        ];
    }
}
