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
            'installments' => 'required|array',
            'principal_amount' => 'required|numeric',
            'type' => ['required' , 'in:' .implode(',',Loan::getLoanTypes())],
            'number_of_installments' => "required",
            'status' => "required",
            'year' => "required",
            'due_date' => "required",
            'issue_date' => "required",
            'end_date' => "required",
        ];
    }
    public function messages()
    {
        return [
            'installments.required'=>'لطفا اقساط را وارد کنید.',
            'principal_amount.required' => 'لطفا مبلغ وام را انتخاب کنید!',
            'principal_amount.numeric' => 'لطفا مبلغ وام را به درستی وارد کنید!',
            'type.required' => 'لطفا نوع وام را وارد کنید!',
            'type.in' => 'نوع وام صحیح نیست!',
            'number_of_installments.required' => 'لطفا تعداد اقساط را وارد کنید!',
            'status.required' => 'لطفا وضعیت وام را وارد کنید!',
            'year.required' => 'لطفا سال جاری را وارد کنید!',
            'due_date.required' => 'لطفا شروع بازپرداخت را وارد کنید!',
            'issue_date.required' => 'لطفا تاریخ صدور وام را وارد کنید!',
            'end_date.required' => 'لطفا تاریخ پایان را وارد کنید!',
        ];
    }
}
