<?php

namespace App\Http\Requests\Client\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ClientSocialLoginRequest extends FormRequest
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
        return [
            'user_id' => 'exists:users,id|required',
            'email' => 'email|nullable',
            'phone' => 'nullable',
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'social' => 'required',
            'device_type' => 'nullable'
        ];
    }
}
