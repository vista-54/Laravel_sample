<?php

namespace App\Http\Requests\Admin\ContactsTerm;

use Illuminate\Foundation\Http\FormRequest;

class ContactsTermUpdateRequest extends FormRequest
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
            'company_name' => 'string|max:500|nullable',
            'address' => 'string|max:500|nullable',
            'website' => 'max:500|nullable',
            'email' => 'string|max:255|nullable',
            'phone' => 'string|max:255|nullable',
            'conditions' => 'string|nullable'
        ];
    }
}
