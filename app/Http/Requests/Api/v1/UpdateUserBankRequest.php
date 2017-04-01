<?php

namespace App\Http\Requests\Api\v1;


class UpdateUserBankRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $cardInfo = $this->route('bank');
        return [
            'card_number' => 'required|digits_between:16,19|unique:user_bank,card_number,' . $cardInfo->id,
            'card_type' => 'required',
            'card_holder' => 'required',
            'bank_province' => 'required|integer',
            'bank_city' => 'required|integer',
            'bank_name' => 'required'
        ];
    }
}
