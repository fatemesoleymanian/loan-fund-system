<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
            'fund_account_id' => 'required|exists:fund_accounts,id',
            'description' => 'required',
            'monthly_charge_id' => 'nullable|exists:monthly_charges,id',
            'installment_id' => 'nullable|exists:installments,id',
            'loan_id' => 'nullable|exists:loans,id',
            'type' => ['required' , 'in:' .implode(',',Transaction::getTransactionTypes())],
        ];
    }
    public function messages()
    {
        return [
            'account_id.required' => 'لطفا شماره حساب را وارد کنید!',
            'amount.required' => 'لطفا مبلغ را وارد کنید!',
            'fund_account_id.required' => 'لطفا صندوق را وارد کنید!',
            'monthly_charge_id.exists' => 'ماهیانه وجود ندارد!',
            'installment_id.exists' => 'قسط / وام وجود ندارد!',
            'loan_id.exists' => ' وام وجود ندارد!',
            'type.in' => 'لطفا نوع تراکنش را وارد کنید!',
            'description.required' => 'لطفا توضیح تراکنش را وارد کنید!',
        ];
    }
}
