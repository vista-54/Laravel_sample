<?php

namespace App\Http\Requests\Admin\Pass;

use Illuminate\Foundation\Http\FormRequest;

class PassUpdateRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'string|max:255|nullable',
            'value' => 'integer|min:0',
            'availability_count' => 'integer',
            'start_date' => 'before:end_date|nullable',
            'end_date' => 'nullable',
            'status' => 'boolean',
            'margin_value' => 'string|nullable',
            'expiration_date' => 'date|nullable',
        ];
    }
}
