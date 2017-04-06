<?php

namespace App\Http\Requests\Admin;


class UpdatePaymentChannelRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $info = $this->route('payment_channel');
        return [
            'name' => 'sometimes|required|max:20|unique:payment_channel,name,' . $info->id,
            'identification_code' => 'sometimes|required|unique:payment_channel,identification_code,' . $info->id,
            'type' => 'sometimes|required|between:1,2',
            'status' => 'sometimes|required',
            'icon' => 'sometimes|required'
        ];
    }
}
