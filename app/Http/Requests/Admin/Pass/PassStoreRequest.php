<?php

namespace App\Http\Requests\Admin\Pass;

use Illuminate\Foundation\Http\FormRequest;

class PassStoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'string|max:255|nullable',
            'margin_value' => 'string|nullable',
            'expiration_date' => 'date|nullable',
        ];
    }
}
