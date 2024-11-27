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
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return   [
            'fund_account_id' => "required|exists:fund_account,id",
            'title' => "required",
            'cost' => "nullable|numeric",
        ];
    }
    public function messages()
    {
        return [
            'fund_account_id.required' => 'لطفا صندوق را وارد کنید!',
            'fund_account_id.exists' => 'صندوق وجود ندارد!',
            'title.required' => 'لطفا نام اثاثیه را وارد کنید!',
            'cost.numeric' => 'قیمت صحیح نیست!',
        ];
    }
}
