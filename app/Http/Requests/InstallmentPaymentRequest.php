<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallmentPaymentRequest extends FormRequest
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
            'id' => "required|exists:installments,id",
            'loan_id' => "nullable|exists:loans,id",
            'loan_account_id' => "nullable|exists:loan_accounts,id",
            'monthly_charge_id' => "nullable|exists:monthly_charges,id",
            'account_id' => "required|exists:accounts,id",
            'account_name' => "required",
            'year' => "nullable|numeric",
            'inst_number' => "required|numeric",
            'amount' => "required|numeric|min:0",
            'due_date' => "required",
            'paid_date' => "required",
            'delay_days' => "required",
            'type' => "required|min:1|max:2",
            'title' => "required",
        ];
    }
    public function messages()
    {
        return [
            'member_id.required' => 'لطفا عضو مربوطه را انتخاب کنید!',
//            'balance.required' => 'لطفا موجودی حساب را وارد کنید!',
            'stock_units.required' => 'لطفا تعداد سهم را وارد کنید!',
            'member_name.required' => 'لطفا نام عضو مربوطه را وارد کنید!',
            'status.required' => 'لطفا وضعیت حساب را وارد کنید!',
            'is_open.required' => 'لطفا باز/بسته بودن حساب را وارد کنید!',
        ];
    }
}
