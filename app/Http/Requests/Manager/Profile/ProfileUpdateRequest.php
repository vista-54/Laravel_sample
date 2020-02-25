<?php

namespace App\Http\Requests\Manager\Profile;

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
            'name' => 'string',
            'email' => ['email', Rule::unique('area_managers')->where(function ($q) {
                $q->where('user_id', auth()->user()->user_id);
            })
                ->ignore(auth()->user()->id)],
        ];
    }
}
