<?php

namespace App\Http\Requests\Manager;

use App\Models\ClientShop;
use Illuminate\Foundation\Http\FormRequest;

class ScanRequest extends FormRequest
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
        if ($this->input('type') == ClientShop::TYPE_LOYALTY) {
            return [
                'type' => 'string',
                'shop_id' => 'required|exists:shops,id',
                'amount' => 'required|numeric|max:1000000000'
            ];
        }
        if ($this->input('type') == ClientShop::TYPE_OFFER) {
            return [
                'amount' => 'required|numeric|max:1000000000',
                'type' => 'string',
                'shop_id' => 'required|exists:shops,id',
                'card_id' => 'required|exists:offers,id',
            ];
        }
        if (($this->input('type') == ClientShop::TYPE_COUPON)) {
            return [
                'type' => 'string',
                'shop_id' => 'required|exists:shops,id',
                'card_id' => 'required|exists:passes,id'
            ];
        }
        abort(422, 'Select a valid transaction type');
        return [];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'amount.max' => 'The amount can not be greater then one billion',
            'card_id.required'  => 'Please choose the offer',
        ];
    }
}
