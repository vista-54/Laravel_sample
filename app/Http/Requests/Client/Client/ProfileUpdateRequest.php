<?php

namespace App\Http\Requests\Client\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => ['email', Rule::unique('clients')->where(function ($q) {
                $q->where('user_id', auth()->user()->user_id);
            })
            ->ignore(auth()->user()->id)],
            'phone' => ['required', Rule::unique('clients')->where(function ($q) {
                $q->where('user_id', auth()->user()->user_id);
            })->ignore(auth()->user()->id)],
            'address' => 'nullable',
            'timezone' => 'nullable',
            'birthday' => 'nullable',
            'race' => 'string',
            'country_code' => 'nullable'
        ];
    }
}
