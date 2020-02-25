<?php

namespace App\Http\Requests\Admin\AreaManager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaManagerUpdateRequest extends FormRequest
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
        $data = [
            'email' =>['email', Rule::unique('area_managers')->where(function ($q) {
                $q->where('user_id', auth()->id());
            })->ignore($this->route('area_manager')->id)],
            'name' => 'string',
        ];
        if ($this->input('password')) {
            $data['password'] = 'string|min:6';
        }
        return $data;
    }
}
