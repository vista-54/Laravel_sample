<?php

namespace App\Http\Requests\Admin\LoyaltyProgram;

use Illuminate\Foundation\Http\FormRequest;

class LoyaltyProgramStoreRequest extends FormRequest
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
            'description' => 'string|max:1000|nullable',
            'country' => 'required|string|max:255',
            'language' => 'required|string|max:255',
//            'icon' => 'string|max:255|nullable',
            'link' => 'string|max:500|nullable',
            'start_at' => 'integer',
        ];
    }
}
