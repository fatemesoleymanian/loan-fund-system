<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class AccountAndMemberRequest extends FormRequest
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
//            'fund_account_id' => "required|exists:fund_accounts,id",
            'balance' => "required|numeric|min:0",
            'stock_units' => "required",
//            'member_name' => "required",
            'status' => ['required' , 'in:' .implode(',',Account::getAccountStatus())],
            'is_open' => "required",
            'full_name' => "required",
            'mobile_number' => "required",
            'telephone_number' => "required",
        ];
    }
    public function messages()
    {
        return [
//            'member_id.required' => 'لطفا عضو مربوطه را انتخاب کنید!',
            'balance.required' => 'لطفا موجودی حساب را وارد کنید!',
            'balance.numeric' => 'لطفا موجودی حساب را وارد کنید!',
            'stock_units.required' => 'لطفا تعداد سهم را وارد کنید!',
//            'member_name.required' => 'لطفا نام عضو مربوطه را وارد کنید!',
            'status.required' => 'لطفا وضعیت حساب را وارد کنید!',
            'is_open.required' => 'لطفا باز/بسته بودن حساب را وارد کنید!',
            'full_name.required' => 'لطفا نام عضو را وارد کنید!',
            'mobile_number.required' => 'لطفا شماره تلفن همراه عضو را وارد کنید!',
            'telephone_number.required' => 'لطفا شماره تلفن عضو را وارد کنید!',
        ];
    }
}
