<?php

namespace App\Http\Requests\POS;

use App\Models\Client;
use App\Models\ClientShop;
use App\Rules\ClientsPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class POSPhoneRequest extends FormRequest
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
            'phone' => ['string', new ClientsPhoneRule()],
            'items' => 'array',
            'items.*.name' => 'required|string|max:191',
            'items.*.value' => 'required|numeric',
            'items.*.quantity' => 'required|integer'
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
