<?php

namespace App\Http\Requests\Admin\PassTemplate;

use Illuminate\Foundation\Http\FormRequest;

class PassTemplateUpdateRequest extends FormRequest
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
            'background_color' => 'nullable',
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
            'back_side_head' => 'nullable',
            'back_side_value' => 'nullable',
//            'icon' => 'nullable',
//            'background_image' => 'nullable',
//            'stripe_image' => 'nullable',
            'customer_id' => 'nullable',
            'unlimited' => 'boolean'
        ];
    }
}
