<?php

namespace App\Http\Requests\Admin\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->role === User::ROLE_SUPER_ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'department' => 'string:255|nullable',
            'email' => 'required|unique:users,email|string:255',
            'password' => 'required|min:6|string:255',
//            'role' => ['required', Rule::in([User::ROLE_MERCHANT, User::ROLE_MANAGER])],
            'business' => 'string:255|nullable',
            'first_name' => 'string:255|nullable',
            'last_name' => 'string:255|nullable',
            'address' => 'string:255|nullable',
            'timezone' => 'string:255|nullable',
            'verified' => [Rule::in([1,0])],
            'app_url' => 'string|nullable',
        ];
    }

    public function data()
    {
        return $this->except('password')
            + [
                'password' => \Hash::make($this->input('password')),
                'role' => User::ROLE_MERCHANT
            ];
    }
}
