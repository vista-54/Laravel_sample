<?php

namespace App\Http\Requests\Admin\LoyaltyProgram;;

use Illuminate\Foundation\Http\FormRequest;

class LoyaltyProgramUpdateRequest extends FormRequest
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
            'user_id' => 'exists:users,id',
            'title' => 'string|max:255',
            'description' => 'string|max:1000|nullable',
            'country' => 'string|max:255',
            'language' => 'string|max:255',
            'currency' => 'nullable',
            'currency_value' => 'nullable',
            'link' => 'string|max:500|nullable',
            'start_at' => 'integer',

            'company_name' => 'string|max:500|nullable',
            'address' => 'string|max:500|nullable',
            'website' => 'max:500|nullable',
            'email' => 'string|max:255|nullable',
            'phone' => 'string|max:255|nullable',
            'conditions' => 'string|nullable',

//            'loyalty_program_id' => 'required|exists:loyalty_programs,id',
            'set_email' => 'integer',
            'set_phone' => 'integer',
            'set_card' => 'integer',
            'scan_card' => 'integer',
        ];
    }
}
