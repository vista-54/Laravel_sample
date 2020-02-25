<?php

namespace App\Http\Requests\Admin\Offer;

use Illuminate\Foundation\Http\FormRequest;

class OfferUpdateRequest extends FormRequest
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
            'name' => 'string|max:255',
            'description' => 'string|max:1000',
            'start_date' => 'before:end_date|nullable',
            'end_date' => 'nullable',
            'points_cost' => 'string',
            'customer_limit' => 'nullable',
            'availability_count' => 'integer',
            'notify' => 'nullable',
            'status' => 'boolean',
            'margin_value' => 'numeric|nullable',
        ];
    }

    public function data()
    {
        $data = $this->validated();
        if ($this->exists('points_cost') && $data['points_cost'] === '-') {
            $data['points_cost'] = 0;
        }
        return $data;
    }
}
