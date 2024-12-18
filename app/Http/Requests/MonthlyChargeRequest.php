<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyChargeRequest extends FormRequest
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
//            'fund_account_id' => ['required' | 'exists:fund_accounts,id'],
            'year' => "required",
            'title' => "required",
            'amount' => "required",
//            'amount' => ['required' | 'numeric'],
        ];
    }
    public function messages()
    {
        return [
//            'fund_account_id.required' => 'لطفا نام صندوق را وارد کنید!',
//            'fund_account_id.exists' => 'صندوق وجود ندارد!',
            'year.required' => 'لطفا سال را وارد کنید!',
            'title.required' => 'لطفا عنوان را وارد کنید!',
            'amount.required' => 'لطفا مبلغ ماهیانه را وارد کنید!',
//            'amount.numeric' => 'لطفا مبلغ ماهیانه را به درستی وارد کنید!',
        ];
    }
}
