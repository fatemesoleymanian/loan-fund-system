<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
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
            'full_name' => "required",
            'mobile_number' => "required",
            'telephone_number' => "required",
        ];
    }
    public function messages()
    {
        return [
            'full_name.required' => 'لطفا نام عضو را وارد کنید!',
            'mobile_number.required' => 'لطفا شماره تلفن همراه عضو را وارد کنید!',
            'telephone_number.required' => 'لطفا شماره تلفن عضو را وارد کنید!',
        ];
    }
}
