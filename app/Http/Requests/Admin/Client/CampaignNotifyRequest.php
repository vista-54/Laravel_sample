<?php

namespace App\Http\Requests\Admin\Client;

use Illuminate\Foundation\Http\FormRequest;

class CampaignNotifyRequest extends FormRequest
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
            'shop_id' => 'nullable',
            'race' => 'string|nullable',
            'birthday' => 'integer|nullable',
            'age' => 'string|nullable',
            'customer_type' => 'string|nullable',
            'type' => 'string|nullable',
            'campaign_start' => 'required',
            'campaign_end' => 'required',
            'campaign_name' => 'nullable',
            'purpose' => 'nullable',
            'frequency' => 'nullable',
            'region' => 'required',
            'trans_total_value' => 'string|required',
            'media' => 'string|nullable',
            'client_group_id' => 'string|nullable',
            'margin_value' => 'string|nullable',
        ];
    }


    public function messages()
    {
        return [
            'text.required' => 'Message field is required',
            'age.required' => 'Please select an age range'
        ];
    }
}
