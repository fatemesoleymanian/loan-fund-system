<?php

namespace App\Http\Requests;

use App\Models\charity;
use Illuminate\Foundation\Http\FormRequest;

class CharityRequest extends FormRequest
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
            'description' => 'required',
            'amount' => "required|numeric|min:0",
            'money_source' => ['required' , 'in:' .implode(',',Charity::getMoneySource())],
        ];
    }
    public function messages()
    {
        return [
            'description.required' => 'لطفا توضیح را وارد کنید!',
            'amount.numeric' => 'هزینه صحیح نیست!',
            'money_source.in' => 'منبع برداشت هزینه صحیح نیست!',
        ];
    }
}
