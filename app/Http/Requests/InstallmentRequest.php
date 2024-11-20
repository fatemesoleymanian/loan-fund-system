<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallmentRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        // Check if request contains multiple installments
        $isArrayRequest = is_array($this->get('installments'));

        if ($isArrayRequest) {
            return [
                'installments' => 'required|array',
                'installments.*.loan_id' => 'required|exists:loans,id',
                'installments.*.inst_number' => 'required|integer',
                'installments.*.amount_due' => 'required|numeric|min:0',
                'installments.*.due_date' => 'required',
            ];
        }

        // Single installment validation
        return [
            'loan_id' => 'required|exists:loans,id',
            'inst_number' => 'required|integer',
            'amount_due' => 'required|numeric|min:0',
            'due_date' => 'required',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'loan_id.required' => 'لطفا وام را انتخاب کنید!',
            'inst_number.required' => 'لطفا شماره قسط را وارد کنید!',
            'amount_due.required' => 'لطفا مبلغ اقساط را وارد کنید!',
            'due_date.required' => 'لطفا تاریخ سررسید را وارد کنید!',
            'installments.*.loan_id.required' => 'لطفا وام را برای هر قسط انتخاب کنید!',
            'installments.*.inst_number.required' => 'لطفا شماره قسط را وارد کنید!',
            'installments.*.amount_due.required' => 'لطفا مبلغ اقساط را وارد کنید!',
            'installments.*.due_date.required' => 'لطفا تاریخ سررسید را وارد کنید!',
        ];
    }
}
