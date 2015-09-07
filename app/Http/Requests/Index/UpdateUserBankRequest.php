<?php

namespace App\Http\Requests\Index;


class UpdateUserBankRequest extends Request
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
            'card_number' => 'required|digits_between:16,18|unique:user_bank,card_number,' . $cardInfo->id,
            'card_type' => 'required',
            'card_holder' => 'required',
            'card_address' => 'required'
        ];
    }
}
