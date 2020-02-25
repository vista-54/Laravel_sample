<?php

namespace App\Http\Requests\Admin\Location;

use Illuminate\Foundation\Http\FormRequest;

class LocationUpdateRequest extends FormRequest
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
            'loyalty_program_id' => 'required|exists:loyalty_programs,id',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'params' => 'string|nullable'
        ];
    }
}
