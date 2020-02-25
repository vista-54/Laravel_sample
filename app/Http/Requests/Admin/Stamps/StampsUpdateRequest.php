<?php

namespace App\Http\Requests\Admin\Stamps;

use Illuminate\Foundation\Http\FormRequest;

class StampsUpdateRequest extends FormRequest
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
            'stamps_number' => 'integer',
            'background_color' => 'string|max:255',
//            'background_image' => 'nullable',
            'stamp_color' => 'string|max:255',
            'unstamp_color' => 'string|max:255',
//            'stamp_image' => 'nullable',
//            'unstamp_image' => 'nullable',
        ];
    }
}
