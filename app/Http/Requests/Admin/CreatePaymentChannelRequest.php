<?php

namespace App\Http\Requests\Admin;


class CreatePaymentChannelRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:20|unique:payment_channel',
            'identification_code' => 'required|unique:payment_channel',
            'type' => 'required|between:1,2',
            'icon' => 'required'
        ];
    }
}
