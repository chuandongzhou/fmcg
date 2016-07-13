<?php

namespace App\Http\Requests\Api\v1;


class CreateUserBankRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'card_number' =>'required|digits_between:16,19|unique:user_bank',
            'card_type' =>'required',
            'card_holder' => 'required',
            'card_address' => 'required'
        ];
    }
}
