<?php

namespace App\Http\Requests;

use App\Models\Loan;
use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
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
            'title' => 'required',
            'static_fee' => 'required|numeric|min:0',
            'fee_percent' => 'required|numeric|min:0',
            'number_of_installments' => 'required|numeric|min:0',
            'installment_interval' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0',
            'min_amount' => 'required|numeric|min:0',
            'emergency' => 'required|boolean',
            'no_need_to_pay' => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [
            'title.required'=>'لطفا عنوان را وارد کنید.',
            'static_fee.required' => 'لطفا کارمزد ثابت را انتخاب کنید!',
            'fee_percent.required' => 'لطفا درصد کارمزد را وارد کنید!',
            'number_of_installments.required' => 'لطفا تعداد اقساط را وارد کنید!',
            'max_amount.required' => 'لطفا سقف وام را وارد کنید!',
            'min_amount.required' => 'لطفا حداقل مبلغ وام را وارد کنید!',
        ];
    }
}
