<?php

namespace App\Http\Requests\Index;


class CreateUserBankRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'card_number' =>'required|digits_between:16,18|unique:user_bank',
            'card_type' =>'required',
            'card_holder' => 'required',
            'card_address' => 'required'
        ];
    }
}
