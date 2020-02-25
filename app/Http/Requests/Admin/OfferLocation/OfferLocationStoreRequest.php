<?php

namespace App\Http\Requests\Admin\OfferLocation;

use Illuminate\Foundation\Http\FormRequest;

class OfferLocationStoreRequest extends FormRequest
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
            'offer_id' => 'required|exists:offers,id',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'params' => 'string|nullable'
        ];
    }
}
