<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetRequest extends FormRequest
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
            'title' => "required",
            'cost' => "nullable|numeric",
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'لطفا نام اثاثیه را وارد کنید!',
            'cost.numeric' => 'قیمت صحیح نیست!',
        ];
    }
}
