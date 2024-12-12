<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanAndPartitionRequest extends FormRequest
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
            'account_id' => 'required|exists:accounts,id',
            'fund_account_id' => 'required|exists:fund_accounts,id',
            'amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'granted_at' => 'required',
            'payback_at' => 'required',
            'number_of_installments' => 'required|numeric|min:0',
            'no_of_paid_inst' => 'required|numeric|min:0',
            'fee_amount' => 'required|numeric|min:0',
            'account_name' => 'required',

        ];
    }
    public function messages()
    {
        return [
            'loan_id.required' => 'لطفا نوع وام را انتخاب کنید!',
            'account_id.required' => 'لطفا شماره حساب را وارد کنید!',
            'fund_account_id.required' => 'لطفا صندوق را وارد کنید!',
            'amount.required' => 'لطفا مبلغ وام را وارد کنید!',
            'paid_amount.required' => 'لطفا مبلغ پرداخت شده را وارد کنید!',
            'granted_at.required' => 'لطفا تاریخ اعطای وام را وارد کنید!',
            'payback_at.required' => 'لطفا تاریخ شروع بازپرداخت را وارد کنید!',
            'number_of_installments.required' => 'لطفا تعداد اقساط را وارد کنید!',
            'no_of_paid_inst.required' => 'لطفا تعداد اقساط پرداخت شده را وارد کنید!',
            'fee_amount.required' => 'لطفا مبلغ کارمزد را وارد کنید!',
            'account_name.required' => 'لطفا نام حساب را وارد کنید!',
        ];
    }
}
