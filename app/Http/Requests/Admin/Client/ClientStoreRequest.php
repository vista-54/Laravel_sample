<?php

namespace App\Http\Requests\Admin\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientStoreRequest extends FormRequest
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
            'phone' => 'required|unique:clients,phone,NULL,id,user_id,'.auth()->id(),
            'email' => 'required|string|email|max:255|unique:clients,email,NULL,id,user_id,'.auth()->id(),
            'password' => 'required|string|min:6',
            'address' => 'max:191|nullable',
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'birthday' => 'nullable',
            'race' => 'string'
        ];
    }

    public function data()
    {
        return $this->except(['password']) + ['password' => \Hash::make($this->password)];
    }
}
