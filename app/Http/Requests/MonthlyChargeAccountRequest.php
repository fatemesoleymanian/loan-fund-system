<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyChargeAccountRequest extends FormRequest
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
            'monthly_charge_id' => 'required|exists:monthly_charges,id'
        ];
    }
    public function messages()
    {
        return [
            'account_id.required' => 'لطفا شماره حساب را وارد کنید!',
            'monthly_charge_id.required' => 'لطفا ماهیانه را وارد کنید!',
        ];
    }
}
