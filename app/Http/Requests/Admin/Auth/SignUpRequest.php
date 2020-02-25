<?php

namespace App\Http\Requests\Admin\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

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
            'department' => 'nullable',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'max:191',
        ];
    }

    public function data()
    {
        return $this->except(['password']) + [
            'password' => \Hash::make($this->password),
                'token' => Str::random(32),
                'role' => User::ROLE_MERCHANT
                ];
    }
}
