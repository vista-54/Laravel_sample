<?php

namespace App\Http\Requests\Admin\PosTerminal;

use Illuminate\Foundation\Http\FormRequest;

class PosTerminalStoreRequest extends FormRequest
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
            'name' => 'required|string|email|max:255|unique:pos_terminals,name,NULL,id,user_id,'.auth()->id(),
            'password' => 'required|string|min:6'
        ];
    }
}
