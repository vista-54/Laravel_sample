<?php

namespace App\Http\Requests\POS;

use App\Models\Client;
use App\Models\ClientShop;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class POSRequest extends FormRequest
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
        $ids = auth()->user()->user->clients()->pluck('id');
        $items = [
            'client_id' => ['integer', 'exists:clients,id', Rule::in($ids)],
            'items' => 'array',
            'items.*.name' => 'required|string|max:191',
            'items.*.value' => 'required|numeric',
            'items.*.quantity' => 'required|integer',

            'ticket_id' => 'string|nullable',
            'ticket_date' => 'string|nullable',
            'store_id' => 'string|nullable',
            'cashier_id' => 'string|nullable',
            'payment_type' => 'string|nullable',
            'stock_code' => 'string|nullable',
            'pack' => 'string|nullable',
            'quantity' => 'string|nullable',
            'unit_price' => 'string|nullable',
            'discount' => 'string|nullable',
            'discount_2' => 'string|nullable',
            'discount_3' => 'string|nullable',
            'amount' => 'string|nullable',
            'coupon_id' => 'string|nullable',
            'loyalty_id' => 'string|nullable',
            'campaign_id' => 'string|nullable',
            'offer_id' => 'string|nullable',
        ];
        if ($this->input('type') == ClientShop::TYPE_LOYALTY) {
            return [
                    'type' => 'string',
                    'amount' => 'required|numeric|max:1000000000'
                ] + $items;
        }
        if ($this->input('type') == ClientShop::TYPE_OFFER) {
            return [
                    'amount' => 'required|numeric|max:1000000000',
                    'type' => 'string',
                    'offer_id' => 'required|exists:offers,id',
                ] + $items;
        }
        if (($this->input('type') == ClientShop::TYPE_COUPON)) {
            return [
                    'type' => 'string',
                    'coupon_id' => 'required|exists:passes,id'
                ] + $items;
        }
//        abort(422, 'Select a valid transaction type');
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
