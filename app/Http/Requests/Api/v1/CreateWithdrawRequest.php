<?php

namespace App\Http\Requests\Api\v1;


class CreateWithdrawRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:1000',
            'bank_id' => 'required|min:1'
        ];
    }
}
