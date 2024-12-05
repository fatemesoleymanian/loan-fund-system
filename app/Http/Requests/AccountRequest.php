<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
            'member_id' => "required|exists:members,id",
//            'balance' => "nullable|numeric",
            'stock_units' => "required",
            'member_name' => "required",
            'status' => ['required' , 'in:' .implode(',',Account::getAccountStatus())],
            'is_open' => "required",
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
