<?php

namespace App\Http\Requests\Admin\ContactsTerm;

use Illuminate\Foundation\Http\FormRequest;

class ContactsTermStoreRequest extends FormRequest
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
            'company_name' => 'required|string|max:500',
            'address' => 'required|string|max:500',
            'website' => 'string|max:500|nullable',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'conditions' => 'string'
        ];
    }
}
