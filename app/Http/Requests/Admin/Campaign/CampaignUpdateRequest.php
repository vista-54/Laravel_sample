<?php

namespace App\Http\Requests\Admin\Campaign;

use Illuminate\Foundation\Http\FormRequest;

class CampaignUpdateRequest extends FormRequest
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
            'age' => 'array|required',
            'customer_type' => 'string|nullable',
            'type' => 'string|nullable',
            'campaign_start' => 'present',
            'campaign_end' => 'present',
            'campaign_name' => 'nullable',
            'purpose' => 'nullable',
            'date_time' => 'date',
            'tag' => 'string',
            'frequency' => 'nullable',
            'region' => 'required',
            'trans_total_value' => 'string|required',
            'media' => 'string|nullable',
            'client_group_id' => 'string|nullable',
            'margin_value' => 'string|nullable',
        ];
    }

    public function data()
    {
        return $this->except('age') + [
                'age' => implode(',', $this->age)
            ];
    }

    public function messages()
    {
        return [
            'text.required' => 'Message field is required.',
            'age.required' => 'Please select an age range'
        ];
    }
}
