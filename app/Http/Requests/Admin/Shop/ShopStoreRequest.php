<?php

namespace App\Http\Requests\Admin\Shop;

use Illuminate\Foundation\Http\FormRequest;

class ShopStoreRequest extends FormRequest
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
            'number' => 'required|integer|unique:shops,number,NULL,id,user_id,'.auth()->id(),
            'name' => 'required|string',
            'address' => 'string|max:255',
            'shop_type_id' => 'exists:shop_types,id',
            'cluster' => 'string|nullable',
            'region' => 'string|nullable',
            'area' => 'string|nullable',
            'city' => 'string|nullable',
        ];
    }
}
