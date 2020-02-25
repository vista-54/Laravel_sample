<?php

namespace App\Http\Requests\Client\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'phone' => 'required',
            'email' => 'required|string|email|max:255|unique:clients,email,NULL,id,user_id,'.$this->user_id,
            'password' => 'required|string|min:6',
            'first_name' => 'max:191',
            'last_name' => 'max:191',
            'birthday' => 'nullable',
        ];
    }

    public function data()
    {
        return $this->except(['password']) + ['password' => \Hash::make($this->password)];
    }
}
