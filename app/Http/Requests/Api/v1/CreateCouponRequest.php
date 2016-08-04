<?php

namespace App\Http\Requests\Api\v1;


class CreateCouponRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'start_at' => 'date',
            'end_at' => 'date|after:start_at',
        ];
    }
}
