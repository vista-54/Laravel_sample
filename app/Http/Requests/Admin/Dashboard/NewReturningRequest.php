<?php

namespace App\Http\Requests\Admin\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class NewReturningRequest extends FormRequest
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
            'new_returning.*.name' => 'string|nullable',
            'new_returning.*.value' => 'numeric|nullable',
            'new_returning.*.period' => 'numeric|nullable',
        ];
    }
}
