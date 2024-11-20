<?php

namespace App\Http\Requests;

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
            'balance' => "required",
            'account_number' => "required",
            'member_name' => "required",
            'status' => "required"
        ];
    }
    public function messages()
    {
        return [
            'member_id.required' => 'لطفا عضو مربوطه را انتخاب کنید!',
            'balance.required' => 'لطفا موجودی حساب را وارد کنید!',
            'account_number.required' => 'لطفا شماره حساب را وارد کنید!',
            'member_name.required' => 'لطفا نام عضو مربوطه را وارد کنید!',
            'status.required' => 'لطفا وضعیت حساب را وارد کنید!',
        ];
    }
}
