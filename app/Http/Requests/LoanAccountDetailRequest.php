<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanAccountDetailRequest extends FormRequest
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

    public function rules()
    {
        return   [
            'loan_id' => 'required|exists:loans,id',
            'account_id' => ['required|exists:accounts,id'],
            'paid_amount' => ['required|numeric'],
            'remained_amount' => ['required|numeric'],

        ];
    }
    public function messages()
    {
        return [
            'loan_id.required' => 'لطفا وام را انتخاب کنید!',
            'account_id.required' => 'لطفا شماره حساب عضو مربوطه را وارد کنید!',
            'paid_amount.numeric' => 'لطفا مقدار پرداخت شده را به درستی وارد کنید!',
            'paid_amount.required' => 'لطفا مقدار پرداخت شده را وارد کنید!',
            'remained_amount.required' => 'لطفا مقدار باقیمانده را وارد کنید!',
            'remained_amount.numeric' => 'لطفا مقدار باقیمانده را به درستی وارد کنید!',
        ];
    }
}
