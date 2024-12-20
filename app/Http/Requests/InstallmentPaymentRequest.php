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
//            'paid_date' => "required",
            'delay_days' => "required",
            'type' => "required|min:1|max:2",
            'title' => "required",
        ];
    }
    public function messages()
    {
        return [
            'id.required' => 'لطفا قسط مربوطه را انتخاب کنید!',
            'id.exists' => 'قسط انتخاب شده معتبر نیست!',
            'loan_id.exists' => 'شناسه وام معتبر نیست!',
            'loan_account_id.exists' => ' وام معتبر نیست!',
            'monthly_charge_id.exists' => 'شناسه شارژ ماهانه معتبر نیست!',
            'account_id.required' => 'لطفا حساب مربوطه را انتخاب کنید!',
            'account_id.exists' => 'شناسه حساب معتبر نیست!',
            'account_name.required' => 'لطفا نام حساب را وارد کنید!',
            'year.numeric' => 'سال باید عددی باشد!',
            'inst_number.required' => 'لطفا شماره قسط را وارد کنید!',
            'inst_number.numeric' => 'شماره قسط باید عددی باشد!',
            'amount.required' => 'لطفا مبلغ را وارد کنید!',
            'amount.numeric' => 'مبلغ باید عددی باشد!',
            'amount.min' => 'مبلغ نمی‌تواند منفی باشد!',
            'due_date.required' => 'لطفا تاریخ سررسید را وارد کنید!',
//            'paid_date.required' => 'لطفا تاریخ پرداخت را وارد کنید!',
            'delay_days.required' => 'لطفا تعداد روزهای تاخیر را وارد کنید!',
            'type.required' => 'لطفا نوع را انتخاب کنید!',
            'type.min' => 'نوع باید حداقل ۱ باشد!',
            'type.max' => 'نوع نمی‌تواند بیشتر از ۲ باشد!',
            'title.required' => 'لطفا عنوان را وارد کنید!',
        ];
    }
}
