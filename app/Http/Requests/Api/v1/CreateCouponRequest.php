<?php

namespace App\Http\Requests\Api\v1;

use Carbon\carbon;

class CreateCouponRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $now = Carbon::now();
        return [
            'full' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'start_at' => 'date',
            'end_at' => 'required|date|after:'. $now,
        ];
    }
}
