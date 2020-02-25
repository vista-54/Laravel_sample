<?php

namespace App\Http\Requests\Admin\Card;

use Illuminate\Foundation\Http\FormRequest;

class CardUpdateRequest extends FormRequest
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
            'background_color' => 'string|nullable',
            'background_main_color' => 'string|nullable',
            'foreground_color' => 'string|nullable',
            'label_color' => 'string|nullable',
            'points_head' => 'string|nullable',
            'points_value' => 'string|nullable',
            'customer_head' => 'string|nullable',
            'customer_value' => 'string|nullable',
            'flip_head' => 'string|nullable',
            'flip_value' => 'string|nullable',
            'loyalty_profile' => 'boolean',
            'loyalty_offers' => 'boolean',
            'loyalty_contact' => 'boolean',
            'loyalty_terms' => 'boolean',
            'loyalty_terms_value' => 'string|nullable|max:2000',
            'loyalty_message' => 'boolean',
            'customer_id' => 'string|nullable',
        ];
    }
}
