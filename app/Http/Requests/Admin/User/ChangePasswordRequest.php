<?php

namespace App\Http\Requests\Admin\User;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->id === $this->route('user.id') || auth()->user()->role === User::ROLE_SUPER_ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|string|min:6'
        ];
    }

    public function data()
    {
        return ['password' => Hash::make($this->input('password'))];
    }
}
