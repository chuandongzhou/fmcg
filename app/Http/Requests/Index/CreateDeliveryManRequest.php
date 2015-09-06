<?php

namespace App\Http\Requests\Index;


class CreateDeliveryManRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' =>'required',
            'phone' =>'required|numeric|digits_between:7,14',
        ];
    }
}
