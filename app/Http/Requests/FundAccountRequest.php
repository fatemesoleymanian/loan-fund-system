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
            'balance' => "required|numeric|min:0",
            'total_balance' => "required|numeric|min:0",
            'fees' => "required|numeric|min:0",
            'expenses' => "required|numeric|min:0",
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'لطفا نام صندوق را وارد کنید!',
            'balance.required' => 'لطفا موجودی صندوق را وارد کنید!',
            'total_balance.required' => 'لطفا موجودی کل صندوق را وارد کنید!',
            'fees.required' => 'لطفا مجموع کارمزد را وارد کنید!',
            'expenses.required' => 'لطفا مجموع کارمزد را وارد کنید!',
        ];
    }
}
