<?php

namespace App\Http\Requests\Admin\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientUpdateRequest extends FormRequest
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
            'phone' => [Rule::unique('clients')->where(function ($q) {
                $q->where('user_id', auth()->id());
            })
                ->ignore($this->route('client')->id)],
            'email' => ['email', Rule::unique('clients')->where(function ($q) {
                $q->where('user_id', auth()->id());
            })->ignore($this->route('client')->id)],
            'first_name' => 'string|nullable',
            'last_name' => 'string|nullable',
            'birthday' => 'nullable',
            'address' => 'max:191|nullable',
            'timezone' => 'nullable',
        ];

        if ($this->input('password')) {
            $data['password'] = 'string|min:6';
        }

        return $data;
    }
}
