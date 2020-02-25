<?php

namespace App\Http\Requests\Admin\AreaManager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class AreaManagerStoreRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|email|unique:area_managers',
            'password' => 'required|min:6'
        ];
    }

    public function data()
    {
        return $this->validated() + [
                'user_id' => auth()->user()->id
            ];
    }
}
