<?php

namespace App\Http\Requests\Admin\PassLocation;

use Illuminate\Foundation\Http\FormRequest;

class PassLocationUpdateRequest extends FormRequest
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
            'latitude' => 'required',
            'longitude' => 'required',
            'params' => 'nullable'
        ];
    }
}
