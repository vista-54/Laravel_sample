<?php

namespace App\Http\Requests\Admin\OfferCard;

use Illuminate\Foundation\Http\FormRequest;

class OfferCardUpdateRequest extends FormRequest
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
            'offer_id' => 'exists:offers,id',
            'background_main_color' => 'nullable',
            'foreground_color' => 'nullable',
            'label_color' => 'nullable',
            'points_head' => 'nullable',
            'points_value' => 'nullable',
            'offer_head' => 'nullable',
            'offer_value' => 'nullable',
            'customer_head' => 'nullable',
            'customer_value' => 'nullable',
            'flip_head' => 'nullable',
            'flip_value' => 'nullable',
            'loyalty_active_offer' => 'boolean',
            'loyalty_offers' => 'boolean',
            'loyalty_profile' => 'boolean',
            'loyalty_contact' => 'boolean',
            'loyalty_terms' => 'boolean',
            'loyalty_last_message' => 'boolean',
            'loyalty_message' => 'boolean',
            'customer_id' => 'nullable',
        ];
    }
}
