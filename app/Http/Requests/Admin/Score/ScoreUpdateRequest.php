<?php

namespace App\Http\Requests\Admin\Score;

use Illuminate\Foundation\Http\FormRequest;

class ScoreUpdateRequest extends FormRequest
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
            'loyalty_program_id' => 'required|exists:loyalty_programs,id',
            'set_email' => 'integer',
            'set_phone' => 'integer',
            'set_card' => 'integer',
            'scan_card' => 'integer',
        ];
    }
}
