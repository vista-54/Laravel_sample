<?php

namespace App\Http\Requests\Admin\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientNotifyRequest extends FormRequest
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
            'text' => 'required|max:63',
            'shop_id' => 'exists:shops,id|nullable',
            'race' => 'string|nullable',
            'birthday' => 'integer|nullable',
            'age' => 'integer|nullable',
            'type' => 'integer|nullable',
            'lifetime_value' => 'integer|nullable',
            'tag' => 'string',
        ];
    }
}
