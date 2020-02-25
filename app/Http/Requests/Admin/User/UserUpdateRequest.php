<?php

namespace App\Http\Requests\Admin\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->id === $this->route('user')->id ||
            auth()->user()->role === User::ROLE_SUPER_ADMIN;
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
//            'email' => 'unique:users,email|string:255',
            'email' => ['email', Rule::unique('users')->ignore($this->route('user')->id)],
            'role' => ['sometimes', 'required', Rule::in([User::ROLE_MERCHANT, User::ROLE_MANAGER])],
            'business' => 'string:255|nullable',
            'first_name' => 'string:255|nullable',
            'last_name' => 'string:255|nullable',
            'address' => 'string:255|nullable',
            'timezone' => 'string:255|nullable',
            'verified' => ['sometimes', 'required', Rule::in([1,0])],
            'app_url' => 'string|nullable',
            'android_version' => 'string|nullable',
            'ios_version' => 'string|nullable',
        ];
    }
}
