<?php

namespace App\Http\Requests\Admin\Offer;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class OfferStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->role === User::ROLE_MERCHANT;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
//            'loyalty_program_id' => 'exists:loyalty_programs,id',
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'start_date' => 'before:end_date|nullable',
            'end_date' => 'nullable',
            'points_cost' => 'required|string',
            'customer_limit' => 'nullable',
            'availability_count' => 'integer',
            'notify' => 'nullable',
            'status' => 'boolean',
            'margin_value' => 'string|nullable',
        ];
    }

    public function data()
    {
        $data = $this->validated();
        if ($data['points_cost'] === '-') {
            $data['points_cost'] = 0;
        }
        return $data;
    }
}
