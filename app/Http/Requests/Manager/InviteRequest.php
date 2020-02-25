<?php

namespace App\Http\Requests\Manager;

use App\Models\AreaManager;
use App\Models\Invite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InviteRequest extends FormRequest
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
            'email' => [
                'required',
                'email'
//                Rule::unique('invites')->queryCallbacks()->where(function ($q) {
//                    /** @var Invite $q */
////                    dd($q);
//                    $q->where('area_manager_id', auth()->user()->id);
////                    $q->whereHas('area_managers', function ($q) {
////                        /** @var AreaManager $q */
////                        $q->where('user_id', auth()->user()->user_id);
////                    });
//                })
            ],
            'shop_id' => 'required|exists:shops,id',
            'type' => 'required',
        ];
    }
}
