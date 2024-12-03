<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
            'description' => 'required',
            'amount' => "required|numeric|min:0",
            'account_id' => 'required|exists:accounts,id',
        ];
    }
    public function messages()
    {
        return [
            'description.required' => 'لطفا توضیح را وارد کنید!',
            'amount.numeric' => 'مبلغ صحیح نیست!',
            'account_id.required' => 'شماره حساب را وارد کنید!×!',
        ];
    }
}
