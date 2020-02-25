<?php

namespace App\Http\Requests\Admin\PosTerminal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PosTerminalUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', Rule::unique('pos_terminals')->ignore($this->route('posTerminal')->id)],
            'password' => 'string|min:6',
        ];
    }
}
